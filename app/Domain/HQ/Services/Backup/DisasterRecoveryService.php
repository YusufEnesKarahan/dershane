<?php

namespace App\Domain\HQ\Services\Backup;

use App\Models\HQDisasterRecoveryPlan;
use App\Models\HQSystemInstance;
use App\Models\HQBackupSnapshot;
use Illuminate\Support\Facades\Log;

class DisasterRecoveryService
{
    /**
     * Execute a DR Plan.
     */
    public function executePlan(HQDisasterRecoveryPlan $plan, string $mode = 'validation')
    {
        $plan->update([
            'status' => 'running',
            'last_run_at' => now(),
        ]);

        Log::info("DisasterRecoveryService: Executing DR plan {$plan->id} in mode: {$mode}");

        $tenant = $plan->tenant;
        $instances = $tenant->systemInstances()->where('status', 'offline')->get();

        $restoreService = app(RestoreService::class);

        foreach ($instances as $instance) {
            // Find the latest snapshot for this instance's jobs
            $snapshot = HQBackupSnapshot::whereHas('job', function ($q) use ($instance) {
                $q->where('system_instance_id', $instance->id)
                  ->where('status', 'completed');
            })->latest()->first();

            if ($snapshot) {
                $restoreService->startRestore($snapshot, $instance, $mode, 'latest');
            }
        }

        $plan->update([
            'status' => 'active'
        ]);
    }
}
