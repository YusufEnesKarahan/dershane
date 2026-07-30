<?php

namespace App\Domain\HQ\Services\Backup;

use App\Models\HQBackupPolicy;
use Illuminate\Support\Facades\Log;

class BackupPolicyService
{
    /**
     * Run scheduled backup policies based on their frequency.
     */
    public function runScheduledPolicies(string $frequency = 'hourly')
    {
        Log::info("BackupPolicyService: Running {$frequency} policies.");

        $policies = HQBackupPolicy::where('is_active', true)
            ->where('frequency', $frequency)
            ->get();

        $orchestration = app(BackupOrchestrationService::class);

        foreach ($policies as $policy) {
            try {
                $orchestration->startBackup($policy);
            } catch (\Exception $e) {
                Log::error("Failed to start backup for policy {$policy->id}: {$e->getMessage()}");
            }
        }
    }
}
