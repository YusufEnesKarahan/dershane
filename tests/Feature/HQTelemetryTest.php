<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\HQTelemetryLog;
use App\Domain\Platform\Services\HQTelemetryService;

class HQTelemetryTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);
        config(['app.installed' => true]);
        config(['hq.enabled' => true]);
    }

    public function test_health_collection()
    {
        $service = app(HQTelemetryService::class);
        $health = $service->collectHealth();
        
        $this->assertArrayHasKey('status', $health);
        $this->assertArrayHasKey('php_version', $health);
        $this->assertArrayHasKey('laravel_version', $health);
        $this->assertTrue(in_array($health['status'], ['healthy', 'degraded']));
    }

    public function test_system_payload()
    {
        $service = app(HQTelemetryService::class);
        $system = $service->collectSystem();
        
        $this->assertArrayHasKey('system_uuid', $system);
        $this->assertArrayHasKey('app_version', $system);
        $this->assertArrayHasKey('environment', $system);
    }

    public function test_snapshot_creation()
    {
        $service = app(HQTelemetryService::class);
        $snapshot = $service->createSnapshot();
        
        $this->assertArrayHasKey('system', $snapshot);
        $this->assertArrayHasKey('health', $snapshot);
        $this->assertArrayHasKey('usage', $snapshot);
        $this->assertArrayHasKey('performance', $snapshot);
        $this->assertArrayHasKey('timestamp', $snapshot);
    }

    public function test_database_logging()
    {
        $service = app(HQTelemetryService::class);
        $snapshot = $service->createSnapshot();
        
        $log = $service->storeSnapshot($snapshot);
        
        $this->assertNotNull($log->uuid);
        $this->assertEquals('snapshot', $log->type);
        $this->assertEquals('success', $log->status);
        $this->assertDatabaseHas('hq_telemetry_logs', [
            'id' => $log->id,
            'type' => 'snapshot'
        ]);
    }

    public function test_admin_page_access()
    {
        $superAdmin = User::factory()->create();
        $superAdmin->roles()->attach(Role::where('name', 'Super Admin')->first()->id);
        
        $response = $this->actingAs($superAdmin)->get('/admin/platform/telemetry');
        
        $response->assertStatus(200);
        $response->assertViewIs('admin.platform.telemetry.index');
        
        $normalUser = User::factory()->create(); // No super admin
        $response2 = $this->actingAs($normalUser)->get('/admin/platform/telemetry');
        
        $response2->assertStatus(403);
    }

    public function test_dashboard_widget()
    {
        $service = app(HQTelemetryService::class);
        $snapshot = $service->createSnapshot();
        $service->storeSnapshot($snapshot);
        
        $superAdmin = User::factory()->create();
        $superAdmin->roles()->attach(Role::where('name', 'Super Admin')->first()->id);
        
        $response = $this->actingAs($superAdmin)->get('/admin/reporting/dashboard');
        
        $response->assertStatus(200);
        $response->assertSee('HQ Telemetry Status');
    }

    public function test_http_send_mock()
    {
        Http::fake([
            '*/telemetry' => Http::response(['success' => true], 200)
        ]);
        
        $superAdmin = User::factory()->create();
        $superAdmin->roles()->attach(Role::where('name', 'Super Admin')->first()->id);
        
        $response = $this->actingAs($superAdmin)->post('/admin/platform/telemetry/send');
        
        $response->assertRedirect('/admin/platform/telemetry');
        $response->assertSessionHas('success');
        
        $this->assertEquals(1, HQTelemetryLog::count());
        $this->assertDatabaseHas('hq_sync_logs', [
            'event_type' => 'telemetry',
            'success' => true
        ]);
    }
}
