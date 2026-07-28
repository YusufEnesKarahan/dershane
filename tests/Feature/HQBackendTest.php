<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\HQTenant;
use App\Models\HQSystemInstance;
use App\Models\HQCentralCommand;

class HQBackendTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);
        config(['hq.api.token' => 'test-hq-token']);
        config(['hq.api.secret' => 'test-secret']);
    }

    protected function getHeaders(array $payload = [])
    {
        $timestamp = time();
        $content = json_encode($payload);
        $signature = hash_hmac('sha256', $content . $timestamp, 'test-secret');

        return [
            'Authorization' => 'Bearer test-hq-token',
            'X-HQ-Signature' => $signature,
            'X-HQ-Timestamp' => (string) $timestamp,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];
    }

    public function test_system_registration()
    {
        $payload = [
            'system_uuid' => 'erp-1234-uuid',
            'system_name' => 'Main Branch ERP',
            'version' => '1.5.0',
            'environment' => 'production'
        ];

        $response = $this->postJson('/api/hq/register', $payload, $this->getHeaders($payload));

        $response->assertStatus(200);
        $response->assertJson(['status' => 'success']);
        
        $this->assertDatabaseHas('hq_system_instances', [
            'system_uuid' => 'erp-1234-uuid',
            'system_name' => 'Main Branch ERP',
        ]);
        
        $this->assertDatabaseHas('hq_tenants', [
            'slug' => 'default',
        ]);
    }

    public function test_heartbeat_update()
    {
        $tenant = HQTenant::create(['name' => 'Tenant 1', 'slug' => 't1']);
        $instance = HQSystemInstance::create([
            'tenant_id' => $tenant->id,
            'system_uuid' => 'erp-uuid',
            'system_name' => 'ERP',
            'system_version' => '1.0',
            'last_seen_at' => now()->subHours(2),
            'status' => 'offline'
        ]);

        $payload = ['system_uuid' => 'erp-uuid'];
        $response = $this->postJson('/api/hq/heartbeat', $payload, $this->getHeaders($payload));

        $response->assertStatus(200);
        
        $instance->refresh();
        $this->assertEquals('online', $instance->status);
        $this->assertTrue(now()->diffInSeconds($instance->last_seen_at) < 5);
    }

    public function test_telemetry_receiving()
    {
        $tenant = HQTenant::create(['name' => 'Tenant 1', 'slug' => 't1']);
        $instance = HQSystemInstance::create([
            'tenant_id' => $tenant->id,
            'system_uuid' => 'erp-uuid',
            'system_name' => 'ERP',
            'system_version' => '1.0',
        ]);

        $payload = [
            'system_uuid' => 'erp-uuid',
            'type' => 'daily_report',
            'cpu' => '45%',
            'memory' => '2GB'
        ];
        
        $response = $this->postJson('/api/hq/telemetry', $payload, $this->getHeaders($payload));
        $response->assertStatus(200);

        $this->assertDatabaseHas('hq_telemetry_records', [
            'system_instance_id' => $instance->id,
            'type' => 'daily_report',
        ]);
    }

    public function test_invalid_signature_rejection()
    {
        $payload = ['system_uuid' => 'test'];
        $timestamp = time();
        $badSignature = 'invalid-hash';

        $headers = [
            'Authorization' => 'Bearer test-hq-token',
            'X-HQ-Signature' => $badSignature,
            'X-HQ-Timestamp' => (string) $timestamp,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];

        $response = $this->postJson('/api/hq/heartbeat', $payload, $headers);
        $response->assertStatus(401);
        $response->assertJson(['error' => 'Invalid signature']);
    }

    public function test_invalid_token_rejection()
    {
        $headers = [
            'Authorization' => 'Bearer wrong-token',
            'X-HQ-Signature' => 'fake',
            'X-HQ-Timestamp' => (string) time(),
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];

        $response = $this->postJson('/api/hq/heartbeat', [], $headers);
        $response->assertStatus(401);
        $response->assertJson(['error' => 'Unauthorized token']);
    }

    public function test_command_queue_retrieval()
    {
        $tenant = HQTenant::create(['name' => 'Tenant 1', 'slug' => 't1']);
        $instance = HQSystemInstance::create([
            'tenant_id' => $tenant->id,
            'system_uuid' => 'erp-uuid',
            'system_name' => 'ERP',
            'system_version' => '1.0',
        ]);

        $command = HQCentralCommand::create([
            'system_instance_id' => $instance->id,
            'command_type' => 'clear_cache',
            'payload' => ['target' => 'all'],
            'status' => 'pending'
        ]);

        $url = '/api/hq/commands?system_uuid=erp-uuid';
        // getJson defaults payload to empty array which json_encodes to '[]'
        $timestamp = time();
        $content = '[]'; 
        $signature = hash_hmac('sha256', $content . $timestamp, 'test-secret');

        $headers = [
            'Authorization' => 'Bearer test-hq-token',
            'X-HQ-Signature' => $signature,
            'X-HQ-Timestamp' => (string) $timestamp,
            'Accept' => 'application/json',
        ];

        $response = $this->getJson($url, $headers);
        $response->assertStatus(200);
        
        $this->assertCount(1, $response->json('commands'));
        
        $command->refresh();
        $this->assertEquals('sent', $command->status);
    }

    public function test_command_result_receiving()
    {
        $tenant = HQTenant::create(['name' => 'Tenant 1', 'slug' => 't1']);
        $instance = HQSystemInstance::create([
            'tenant_id' => $tenant->id,
            'system_uuid' => 'erp-uuid',
            'system_name' => 'ERP',
            'system_version' => '1.0',
        ]);

        $command = HQCentralCommand::create([
            'system_instance_id' => $instance->id,
            'command_type' => 'clear_cache',
            'status' => 'sent'
        ]);

        $payload = [
            'system_uuid' => 'erp-uuid',
            'success' => true,
            'output' => 'Cache cleared successfully'
        ];
        
        $response = $this->postJson('/api/hq/commands/' . $command->id . '/result', $payload, $this->getHeaders($payload));
        $response->assertStatus(200);

        $command->refresh();
        $this->assertEquals('completed', $command->status);
        $this->assertTrue($command->payload['result']['success']);
    }

    public function test_admin_access()
    {
        $superAdmin = User::factory()->create();
        $superAdmin->roles()->attach(Role::where('name', 'Super Admin')->first()->id);
        
        config(['app.installed' => true]);

        $response = $this->actingAs($superAdmin)->get('/admin/platform/hq-central');
        $response->assertStatus(200);
        $response->assertSee('HQ Central Platform');
    }
}
