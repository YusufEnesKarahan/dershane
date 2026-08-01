<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\HQDeployment;
use App\Core\Services\Fleet\RolloutService;

class VerifyHealthJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $deployment;

    /**
     * Create a new job instance.
     */
    public function __construct(HQDeployment $deployment)
    {
        $this->deployment = $deployment;
    }

    /**
     * Execute the job.
     */
    public function handle(RolloutService $rolloutService): void
    {
        // Health check usually takes a bit post deployment to stabilize
        sleep(2);
        
        // Simulating health verification success
        $healthOk = true;

        if ($healthOk) {
            \App\Models\HQDeploymentLog::create([
                'hq_deployment_id' => $this->deployment->id,
                'level' => 'info',
                'message' => 'Post-deployment health verification passed.',
            ]);
            
            $rolloutService->progressRollout($this->deployment);
        } else {
            \App\Models\HQDeploymentLog::create([
                'hq_deployment_id' => $this->deployment->id,
                'level' => 'error',
                'message' => 'Post-deployment health verification failed. Initiating rollback.',
            ]);
            
            $rolloutService->initiateRollback($this->deployment);
        }
    }
}
