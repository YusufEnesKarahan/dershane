<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Role;
use App\Models\HQVersion;
use App\Models\HQUpdateJob;
use App\Models\HQTenant;
use App\Models\HQSystemInstance;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HQUpdateTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);
        
        $superAdmin = User::factory()->create();
        $superAdmin->roles()->attach(Role::where('name', 'Super Admin')->first()->id);
        
        config(['app.installed' => true]);
        config(['hq.api.token' => 'test-hq-token']);
        config(['hq.api.secret' => 'test-secret']);
        
        $this->actingAs($superAdmin);
    }

    public function test_can_publish_new_version()
    {
        $response = $this->post(route('admin.platform.hq_central.versions.store'), [
            'version' => '2.0.0',
            'channel' => 'stable',
            'is_mandatory' => 1,
            'action' => 'publish'
        ]);
        
        $response->assertRedirect(route('admin.platform.hq_central.versions.index'));
        $this->assertDatabaseHas('hq_versions', [
            'version' => '2.0.0',
            'status' => 'published',
            'is_mandatory' => 1
        ]);
    }

    public function test_can_dispatch_single_update()
    {
        $version = HQVersion::create(['version' => '1.0.1', 'status' => 'published', 'channel' => 'stable']);
        $tenant = HQTenant::create(['name' => 'Test Tenant', 'slug' => 'test-tenant']);
        $instance = HQSystemInstance::create([
            'tenant_id' => $tenant->id,
            'system_uuid' => 'sys-123',
            'system_name' => 'Sys123',
            'system_version' => '1.0.0',
            'status' => 'online'
        ]);

        $response = $this->post(route('admin.platform.hq_central.updates.store'), [
            'version_id' => $version->id,
            'target_type' => 'single',
            'system_instance_id' => $instance->id,
        ]);

        $response->assertRedirect(route('admin.platform.hq_central.updates.index'));
        $this->assertDatabaseHas('hq_update_jobs', [
            'version_id' => $version->id,
            'target_type' => 'single',
            'system_instance_id' => $instance->id,
            'status' => 'scheduled'
        ]);
        $this->assertDatabaseHas('hq_central_commands', [
            'system_instance_id' => $instance->id,
            'command_type' => 'start_update'
        ]);
    }

    public function test_api_check_update_finds_latest_mandatory()
    {
        HQVersion::create(['version' => '1.0.0', 'status' => 'published', 'is_mandatory' => false]);
        HQVersion::create(['version' => '2.0.0', 'status' => 'published', 'is_mandatory' => true]);

        $timestamp = time();
        $payload = [
            'system_uuid' => 'dummy',
            'current_version' => '1.5.0'
        ];
        
        $signature = hash_hmac('sha256', json_encode($payload) . $timestamp, 'test-secret');

        $response = $this->postJson('/api/hq/update/check', $payload, [
            'Authorization' => 'Bearer test-hq-token',
            'X-HQ-Signature' => $signature,
            'X-HQ-Timestamp' => (string) $timestamp,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'latest_version' => '2.0.0',
            'needs_update' => true,
            'is_mandatory' => true,
        ]);
    }

    public function test_api_report_update_progress()
    {
        $version = HQVersion::create(['version' => '1.0.1', 'status' => 'published']);
        $job = HQUpdateJob::create([
            'version_id' => $version->id,
            'target_type' => 'single',
            'status' => 'scheduled'
        ]);

        $timestamp = time();
        $payload = [
            'system_uuid' => 'dummy',
            'job_id' => $job->id,
            'progress' => 45
        ];
        
        $signature = hash_hmac('sha256', json_encode($payload) . $timestamp, 'test-secret');

        $response = $this->postJson('/api/hq/update/progress', $payload, [
            'Authorization' => 'Bearer test-hq-token',
            'X-HQ-Signature' => $signature,
            'X-HQ-Timestamp' => (string) $timestamp,
        ]);

        $response->assertStatus(200);
        
        $job->refresh();
        $this->assertEquals(45, $job->progress);
        $this->assertEquals('sent', $job->status);
    }
}
