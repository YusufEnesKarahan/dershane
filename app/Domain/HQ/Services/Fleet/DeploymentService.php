<?php

namespace App\Domain\HQ\Services\Fleet;

use App\Models\HQDeployment;
use App\Models\HQDeploymentTarget;
use Illuminate\Support\Facades\DB;
use App\Models\HQSystemInstance;

class DeploymentService
{
    protected HealthValidationService $healthService;

    public function __construct(HealthValidationService $healthService)
    {
        $this->healthService = $healthService;
    }

    /**
     * Start a deployment.
     */
    public function startDeployment(HQDeployment $deployment)
    {
        $deployment->update([
            'status' => 'running',
            'started_at' => now(),
        ]);

        app(\App\Domain\HQ\Services\HQAuditService::class)->logSystemAction(
            action: 'fleet.deployment.started',
            category: 'fleet',
            severity: 'info',
            description: "Deployment {$deployment->id} for version {$deployment->version} started.",
            metadata: ['deployment_id' => $deployment->id]
        );

        $this->processNextBatch($deployment);
    }

    /**
     * Process next batch based on rollout percentage.
     */
    public function processNextBatch(HQDeployment $deployment)
    {
        if ($deployment->status !== 'running') {
            return;
        }

        // Get pending targets
        $pendingTargets = $deployment->targets()->where('status', 'pending')->get();

        if ($pendingTargets->isEmpty()) {
            $this->completeDeployment($deployment);
            return;
        }

        // Determine how many to pick based on type
        $batchSize = $this->calculateBatchSize($deployment, $pendingTargets->count());
        $batch = $pendingTargets->take($batchSize);

        foreach ($batch as $target) {
            $this->executeTargetDeployment($target);
        }
    }

    protected function calculateBatchSize(HQDeployment $deployment, int $remainingCount): int
    {
        if ($deployment->type === 'manual' || $deployment->type === 'blue-green') {
            return $remainingCount; // Deploy to all targeted
        }
        
        if ($deployment->type === 'canary') {
            return 1; // Deploy to exactly 1 first
        }

        // Staged or Rolling
        $totalTargets = $deployment->targets()->count();
        $percentageToDeploy = $deployment->rollout_percentage > 0 ? $deployment->rollout_percentage : 20;
        
        $batchSize = max(1, (int) round(($percentageToDeploy / 100) * $totalTargets));
        
        return min($batchSize, $remainingCount);
    }

    /**
     * Execute deployment for a single target.
     */
    public function executeTargetDeployment(HQDeploymentTarget $target)
    {
        $target->update(['status' => 'running', 'started_at' => now()]);
        
        // Push to queue for async execution
        \App\Jobs\ProcessDeploymentJob::dispatch($target);
    }

    /**
     * Mark a target as completed and check health.
     */
    public function completeTarget(HQDeploymentTarget $target, bool $success, string $error = null)
    {
        $status = $success ? 'completed' : 'failed';
        $target->update([
            'status' => $status,
            'error_message' => $error,
            'completed_at' => now(),
        ]);

        if (!$success) {
            $this->handleTargetFailure($target);
        } else {
            // Check if we need to progress the batch or finish
            $deployment = $target->deployment;
            $runningCount = $deployment->targets()->where('status', 'running')->count();
            
            if ($runningCount === 0) {
                // Batch is done, verify health if canary/rolling
                if (in_array($deployment->type, ['canary', 'rolling', 'staged'])) {
                    // Automatically dispatch health verification jobs instead of direct service call
                    // to prevent blocking
                    \App\Jobs\VerifyHealthJob::dispatch($deployment);
                } else {
                    $this->processNextBatch($deployment);
                }
            }
        }
    }

    public function completeDeployment(HQDeployment $deployment)
    {
        $deployment->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        app(\App\Domain\HQ\Services\HQAuditService::class)->logSystemAction(
            action: 'fleet.deployment.completed',
            category: 'fleet',
            severity: 'info',
            description: "Deployment {$deployment->id} for version {$deployment->version} completed.",
            metadata: ['deployment_id' => $deployment->id]
        );

        event(new \App\Events\DeploymentCompleted($deployment));
    }

    protected function handleTargetFailure(HQDeploymentTarget $target)
    {
        $deployment = $target->deployment;
        
        $deployment->update(['status' => 'failed', 'completed_at' => now()]);

        app(\App\Domain\HQ\Services\HQAuditService::class)->logSystemAction(
            action: 'fleet.deployment.failed',
            category: 'fleet',
            severity: 'error',
            description: "Deployment {$deployment->id} failed on target {$target->id}.",
            metadata: ['deployment_id' => $deployment->id, 'error' => $target->error_message]
        );

        app(\App\Domain\HQ\Services\HQAlertService::class)->createAlert(
            severity: 'critical',
            title: 'deployment.failed',
            message: "Deployment {$deployment->id} (version {$deployment->version}) failed.",
            metadata: ['target_id' => $target->id, 'error' => $target->error_message]
        );

        event(new \App\Events\DeploymentFailed($deployment));

        // Attempt automatic rollback if rolling/canary
        if (in_array($deployment->type, ['canary', 'rolling', 'staged'])) {
            app(RolloutService::class)->initiateRollback($deployment);
        }
    }
}
