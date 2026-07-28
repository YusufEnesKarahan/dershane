<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Domain\Platform\Services\SignatureService;

class HQCommunicationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);
        config(['app.installed' => true]);
        config(['hq.enabled' => true]);
    }

    public function test_signature()
    {
        $service = app(SignatureService::class);
        $payload = ['foo' => 'bar'];
        $secret = 'test_secret_123';
        
        $signature = $service->generate($payload, $secret);
        
        $expected = hash_hmac('sha256', json_encode($payload), $secret);
        $this->assertEquals($expected, $signature);
    }

    public function test_ping()
    {
        Http::fake([
            '*/ping' => Http::response(['success' => true, 'message' => 'pong'], 200)
        ]);

        $superAdmin = User::factory()->create();
        $superAdmin->roles()->attach(Role::where('name', 'Super Admin')->first()->id);
        
        $response = $this->actingAs($superAdmin)->post('/admin/platform/communication/ping');
        
        $response->assertRedirect('/admin/platform/communication');
        $response->assertSessionHas('success');
        
        $this->assertDatabaseHas('hq_sync_logs', [
            'event_type' => 'ping',
            'success' => true,
            'response_status' => 200,
        ]);
    }

    public function test_health_payload()
    {
        Http::fake([
            '*/health' => Http::response(['success' => true, 'message' => 'healthy'], 200)
        ]);

        $superAdmin = User::factory()->create();
        $superAdmin->roles()->attach(Role::where('name', 'Super Admin')->first()->id);
        
        $response = $this->actingAs($superAdmin)->post('/admin/platform/communication/health');
        
        $response->assertRedirect('/admin/platform/communication');
        $response->assertSessionHas('success');
        
        $this->assertDatabaseHas('hq_sync_logs', [
            'event_type' => 'health',
            'success' => true,
            'response_status' => 200,
        ]);
    }

    public function test_manual_sync()
    {
        Http::fake([
            '*/sync' => Http::response(['success' => true, 'message' => 'synced'], 200)
        ]);

        $superAdmin = User::factory()->create();
        $superAdmin->roles()->attach(Role::where('name', 'Super Admin')->first()->id);
        
        $response = $this->actingAs($superAdmin)->post('/admin/platform/communication/sync');
        
        $response->assertRedirect('/admin/platform/communication');
        $response->assertSessionHas('success');
        
        $this->assertDatabaseHas('hq_sync_logs', [
            'event_type' => 'sync',
            'success' => true,
        ]);
    }

    public function test_failed_request_logged()
    {
        Http::fake([
            '*/ping' => Http::response(['error' => 'Internal Server Error'], 500)
        ]);

        $superAdmin = User::factory()->create();
        $superAdmin->roles()->attach(Role::where('name', 'Super Admin')->first()->id);
        
        $response = $this->actingAs($superAdmin)->post('/admin/platform/communication/ping');
        
        $response->assertRedirect('/admin/platform/communication');
        $response->assertSessionHas('error'); // Should display error because success was false
        
        $this->assertDatabaseHas('hq_sync_logs', [
            'event_type' => 'ping',
            'success' => false,
            'response_status' => 500,
        ]);
    }
}
