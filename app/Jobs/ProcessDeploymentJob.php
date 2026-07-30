<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\HQDeploymentTarget;
use App\Domain\HQ\Services\Fleet\DeploymentService;

class ProcessDeploymentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $target;
    public $isRollback;

    /**
     * Create a new job instance.
     */
    public function __construct(HQDeploymentTarget $target, bool $isRollback = false)
    {
        $this->target = $target;
        $this->isRollback = $isRollback;
    }

    /**
     * Execute the job.
     */
    public function handle(DeploymentService $deploymentService): void
    {
        // Avoid executing if not pending or running
        if (!in_array($this->target->status, ['pending', 'running'])) {
            return;
        }

        try {
            // Simulate deployment or rollback logic via HQRemoteCommandService
            // For now, assume it takes some seconds and succeeds
            sleep(2);
            
            // Generate log
            \App\Models\HQDeploymentLog::create([
                'hq_deployment_id' => $this->target->hq_deployment_id,
                'hq_system_instance_id' => null, // Would be set if targetable is a system instance
                'level' => 'info',
                'message' => $this->isRollback ? 'Rollback executed on target.' : 'Deployment executed on target.',
            ]);

            if ($this->isRollback) {
                $this->target->update(['status' => 'rolled_back', 'completed_at' => now()]);
            } else {
                $deploymentService->completeTarget($this->target, true);
            }
        } catch (\Exception $e) {
            \App\Models\HQDeploymentLog::create([
                'hq_deployment_id' => $this->target->hq_deployment_id,
                'level' => 'error',
                'message' => $this->isRollback ? 'Rollback failed.' : 'Deployment failed.',
                'context' => ['error' => $e->getMessage()]
            ]);
            
            if ($this->isRollback) {
                $this->target->update(['status' => 'failed', 'error_message' => $e->getMessage(), 'completed_at' => now()]);
            } else {
                $deploymentService->completeTarget($this->target, false, $e->getMessage());
            }
        }
    }
}
