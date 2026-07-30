<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\HQReleaseChannel;
use App\Models\HQInstanceGroup;
use App\Models\HQTenant;
use App\Models\HQSystemInstance;
use App\Models\HQDeployment;
use App\Models\HQDeploymentTarget;
use App\Models\HQMaintenanceWindow;
use App\Domain\HQ\Services\Fleet\DeploymentService;
use App\Domain\HQ\Services\Fleet\FleetService;
use App\Domain\HQ\Services\Fleet\MaintenanceService;
use App\Domain\HQ\Services\Fleet\RolloutService;
use Illuminate\Support\Facades\Queue;
use App\Jobs\ProcessDeploymentJob;
use App\Jobs\VerifyHealthJob;

class DeploymentEngineTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Queue::fake();
    }

    public function test_fleet_models_and_relationships()
    {
        $channel = HQReleaseChannel::create(['name' => 'Stable', 'slug' => 'stable']);
        $group = HQInstanceGroup::create(['name' => 'EU Region', 'slug' => 'eu']);

        $tenant = HQTenant::create([
            'name' => 'Test Tenant', 
            'slug' => 'test-tenant',
            'hq_release_channel_id' => $channel->id,
            'hq_instance_group_id' => $group->id
        ]);

        $this->assertEquals($channel->id, $tenant->releaseChannel->id);
        $this->assertEquals($group->id, $tenant->instanceGroup->id);
        
        $this->assertCount(1, $channel->tenants);
        $this->assertCount(1, $group->tenants);

        $deployment = HQDeployment::create([
            'version' => '1.5.0',
            'type' => 'canary',
            'rollout_percentage' => 0,
            'status' => 'queued',
        ]);

        $target = $deployment->targets()->create([
            'targetable_type' => HQTenant::class,
            'targetable_id' => $tenant->id,
            'status' => 'pending'
        ]);

        $this->assertCount(1, $deployment->targets);
        $this->assertEquals(HQTenant::class, $target->targetable_type);
        
        $log = $deployment->logs()->create([
            'level' => 'info',
            'message' => 'Test log'
        ]);
        
        $this->assertCount(1, $deployment->logs);

        // Maintenance Window
        $window = HQMaintenanceWindow::create([
            'targetable_type' => HQTenant::class,
            'targetable_id' => $tenant->id,
            'starts_at' => now()->addHour(),
            'ends_at' => now()->addHours(3),
            'status' => 'scheduled'
        ]);

        $this->assertCount(1, $tenant->maintenanceWindows);
    }

    public function test_deployment_service_starts_and_dispatches_jobs()
    {
        $deployment = HQDeployment::create([
            'version' => '2.0.0',
            'type' => 'canary',
            'status' => 'queued',
        ]);

        $tenant1 = HQTenant::create(['name' => 'T1', 'slug' => 't1']);
        $tenant2 = HQTenant::create(['name' => 'T2', 'slug' => 't2']);

        $deployment->targets()->create(['targetable_type' => HQTenant::class, 'targetable_id' => $tenant1->id]);
        $deployment->targets()->create(['targetable_type' => HQTenant::class, 'targetable_id' => $tenant2->id]);

        $service = app(DeploymentService::class);
        $service->startDeployment($deployment);

        $this->assertEquals('running', $deployment->fresh()->status);
        $this->assertNotNull($deployment->fresh()->started_at);
        
        // Canary mode starts with exactly 1 target
        $runningTargets = $deployment->targets()->where('status', 'running')->get();
        $this->assertCount(1, $runningTargets);
        
        Queue::assertPushed(ProcessDeploymentJob::class, 1);
    }

    public function test_target_completion_progresses_canary()
    {
        $deployment = HQDeployment::create([
            'version' => '2.1.0',
            'type' => 'canary',
            'status' => 'running',
        ]);

        $target = $deployment->targets()->create([
            'targetable_type' => HQTenant::class, 
            'targetable_id' => 1,
            'status' => 'running'
        ]);

        $service = app(DeploymentService::class);
        $service->completeTarget($target, true);

        $this->assertEquals('completed', $target->fresh()->status);
        
        // Since the batch is done (1/1 completed), and type is canary, it dispatches VerifyHealthJob
        Queue::assertPushed(VerifyHealthJob::class, function ($job) use ($deployment) {
            return $job->deployment->id === $deployment->id;
        });
    }

    public function test_target_failure_triggers_rollback()
    {
        $deployment = HQDeployment::create([
            'version' => '2.1.0',
            'type' => 'canary',
            'status' => 'running',
        ]);

        $target = $deployment->targets()->create([
            'targetable_type' => HQTenant::class, 
            'targetable_id' => 1,
            'status' => 'running'
        ]);

        $service = app(DeploymentService::class);
        $service->completeTarget($target, false, 'Simulated failure');

        $this->assertEquals('pending', $target->fresh()->status);
        $this->assertEquals('Simulated failure', $target->fresh()->error_message);
        $this->assertEquals('rollback', $deployment->fresh()->status);
        
        // Rollback implies process job dispatched with rollback flag (simulated in RolloutService)
        Queue::assertPushed(ProcessDeploymentJob::class, function ($job) {
            return $job->isRollback === true;
        });
    }

    public function test_maintenance_automation()
    {
        $tenant = HQTenant::create(['name' => 'T3', 'slug' => 't3']);
        $window = HQMaintenanceWindow::create([
            'targetable_type' => HQTenant::class,
            'targetable_id' => $tenant->id,
            'starts_at' => now()->subMinutes(5), // Should start
            'ends_at' => now()->addHour(),
            'status' => 'scheduled'
        ]);

        $service = app(MaintenanceService::class);
        $service->processScheduledWindows();

        $this->assertEquals('active', $window->fresh()->status);

        // Now move ends_at to past
        $window->update(['ends_at' => now()->subMinutes(1)]);
        
        $service->processScheduledWindows();
        $this->assertEquals('completed', $window->fresh()->status);
    }
    
    public function test_rollout_progresses_percentages()
    {
        $deployment = HQDeployment::create([
            'version' => '3.0.0',
            'type' => 'rolling',
            'status' => 'running',
            'rollout_percentage' => 0
        ]);
        
        // Add multiple targets so it doesn't auto-complete
        for ($i = 1; $i <= 3; $i++) {
            $deployment->targets()->create([
                'targetable_type' => HQTenant::class, 
                'targetable_id' => $i,
                'status' => 'pending'
            ]);
        }
        
        $service = app(RolloutService::class);
        $service->progressRollout($deployment);
        $deployment->refresh();
        $this->assertEquals(5, $deployment->rollout_percentage);
        
        $service->progressRollout($deployment);
        $deployment->refresh();
        $this->assertEquals(10, $deployment->rollout_percentage);
        
        $service->progressRollout($deployment);
        $deployment->refresh();
        $this->assertEquals(25, $deployment->rollout_percentage);
    }
}
