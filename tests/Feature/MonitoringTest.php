<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\SystemMetric;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;

class MonitoringTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_health_endpoint_returns_extended_status()
    {
        $response = $this->get('/health');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'database',
            'cache',
            'queue',
            'storage',
            'disk_usage_percentage',
            'app_version',
            'environment',
        ]);
    }

    public function test_queue_health_endpoint_requires_auth()
    {
        // Guest request should return 401/redirect
        $response = $this->getJson('/health/queue');
        $response->assertStatus(401);

        // Authenticated user request
        $user = User::factory()->create();
        $response = $this->actingAs($user)->getJson('/health/queue');
        $response->assertStatus(200);
        $response->assertJsonStructure(['status', 'failed_jobs', 'queue']);
    }

    public function test_health_details_endpoint_requires_super_admin()
    {
        $user = User::factory()->create();
        
        // Regular user should get 403
        $response = $this->actingAs($user)->getJson('/health/details');
        $response->assertStatus(403);

        // Super Admin user
        $superAdmin = User::factory()->create();
        $role = Role::where('name', 'Super Admin')->first();
        if ($role) {
            $superAdmin->roles()->attach($role->id);
        }

        $response = $this->actingAs($superAdmin)->getJson('/health/details');
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status', 'database', 'cache', 'queue', 'storage', 
            'disk_usage_percentage', 'app_version', 'environment', 'details'
        ]);
    }

    public function test_slow_query_logger_logs_warnings()
    {
        Log::shouldReceive('warning')
            ->once()
            ->withArgs(function ($message, $context) {
                return $message === 'SLOW_QUERY' && isset($context['sql']);
            });

        event(new \Illuminate\Database\Events\QueryExecuted(
            'select * from users',
            [],
            550.00, // 550ms
            DB::connection()
        ));
    }

    public function test_metric_collection_command_stores_metrics()
    {
        $exitCode = Artisan::call('system:collect-metrics');
        $this->assertEquals(0, $exitCode);

        $this->assertDatabaseHas('system_metrics', [
            'metric_name' => 'active_users'
        ]);
        $this->assertDatabaseHas('system_metrics', [
            'metric_name' => 'total_students'
        ]);
        $this->assertDatabaseHas('system_metrics', [
            'metric_name' => 'total_teachers'
        ]);
        $this->assertDatabaseHas('system_metrics', [
            'metric_name' => 'failed_queue_jobs'
        ]);
    }
}
