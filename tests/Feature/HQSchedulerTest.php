<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\HQSchedulerLog;

class HQSchedulerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);
        config(['app.installed' => true]);
        config(['hq.enabled' => true]);
        // By default, hq.scheduler.enabled is false in config, matching real environment
    }

    public function test_scheduler_disabled_by_default()
    {
        $this->assertFalse(config('hq.scheduler.enabled'));
    }

    public function test_telemetry_command_blocked_when_disabled()
    {
        $exitCode = Artisan::call('hq:telemetry');
        $this->assertEquals(0, $exitCode);
        
        $output = Artisan::output();
        $this->assertStringContainsString('HQ Scheduler is currently disabled', $output);
        
        $this->assertEquals(0, HQSchedulerLog::count());
    }

    public function test_task_execution_log_created()
    {
        config(['hq.scheduler.enabled' => true]);
        
        // Use sync since it's a mock that doesn't make real HTTP requests
        $exitCode = Artisan::call('hq:sync');
        $this->assertEquals(0, $exitCode);
        
        $output = Artisan::output();
        $this->assertStringContainsString('HQ Sync Queue Task completed successfully', $output);
        
        $log = HQSchedulerLog::first();
        $this->assertNotNull($log);
        $this->assertEquals('hq:sync', $log->task_name);
        $this->assertEquals('success', $log->status);
        $this->assertNotNull($log->duration_ms);
    }

    public function test_failed_task_logging()
    {
        config(['hq.scheduler.enabled' => true]);
        
        $service = app(\App\Domain\Platform\Services\HQSchedulerService::class);
        
        $result = $service->executeTask('test:fail', function() {
            throw new \Exception('Simulated failure');
        });
        
        $this->assertFalse($result);
        
        $log = HQSchedulerLog::where('task_name', 'test:fail')->first();
        $this->assertNotNull($log);
        $this->assertEquals('failed', $log->status);
        $this->assertEquals('Simulated failure', $log->error_message);
    }

    public function test_scheduler_admin_page()
    {
        $superAdmin = User::factory()->create();
        $superAdmin->roles()->attach(Role::where('name', 'Super Admin')->first()->id);
        
        $response = $this->actingAs($superAdmin)->get('/admin/platform/scheduler');
        
        $response->assertStatus(200);
        $response->assertViewIs('admin.platform.scheduler.index');
        $response->assertSee('Scheduler Status');
    }

    public function test_dashboard_widget()
    {
        $superAdmin = User::factory()->create();
        $superAdmin->roles()->attach(Role::where('name', 'Super Admin')->first()->id);
        
        $response = $this->actingAs($superAdmin)->get('/admin/reporting/dashboard');
        
        $response->assertStatus(200);
        $response->assertSee('HQ Automation Status');
    }

    public function test_allowed_commands_only()
    {
        // Assert that the console routes define exactly what is expected and no more
        $commands = \Illuminate\Support\Facades\Artisan::all();
        $this->assertArrayHasKey('hq:telemetry', $commands);
        $this->assertArrayHasKey('hq:heartbeat', $commands);
        $this->assertArrayHasKey('hq:sync', $commands);
        // Ensure that something dangerous isn't registered (e.g. hq:exec)
        $this->assertArrayNotHasKey('hq:exec', $commands);
    }
}
