<?php

namespace App\Domain\HQ\Services\Fleet;

use App\Models\HQDeployment;

class RolloutService
{
    /**
     * Initiate a rollback for a failed deployment.
     */
    public function initiateRollback(HQDeployment $deployment)
    {
        $deployment->update(['status' => 'rollback']);

        app(\App\Domain\HQ\Services\HQAuditService::class)->logSystemAction(
            action: 'fleet.deployment.rollback_started',
            category: 'fleet',
            severity: 'warning',
            description: "Deployment {$deployment->id} is rolling back.",
            metadata: ['deployment_id' => $deployment->id]
        );

        // Find all completed targets and mark them for rollback
        $targets = $deployment->targets()->whereIn('status', ['completed', 'failed', 'running'])->get();

        foreach ($targets as $target) {
            $target->update(['status' => 'pending']); // Pending for rollback execution
            
            // In a real scenario, we would dispatch a rollback specific job.
            // For now, we simulate by dispatching the process job with a rollback flag in config
            \App\Jobs\ProcessDeploymentJob::dispatch($target, true);
        }
    }

    /**
     * Progress rollout percentage.
     */
    public function progressRollout(HQDeployment $deployment)
    {
        if ($deployment->status !== 'running') {
            return;
        }

        $current = $deployment->rollout_percentage;
        $next = 100;

        if ($current < 5) $next = 5;
        elseif ($current < 10) $next = 10;
        elseif ($current < 25) $next = 25;
        elseif ($current < 50) $next = 50;
        else $next = 100;

        $deployment->update(['rollout_percentage' => $next]);

        app(\App\Domain\HQ\Services\HQAuditService::class)->logSystemAction(
            action: 'fleet.deployment.rollout_progressed',
            category: 'fleet',
            severity: 'info',
            description: "Deployment {$deployment->id} progressed to {$next}%.",
            metadata: ['deployment_id' => $deployment->id, 'percentage' => $next]
        );

        app(DeploymentService::class)->processNextBatch($deployment);
    }
}
