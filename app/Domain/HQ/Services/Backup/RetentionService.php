<?php

namespace App\Domain\HQ\Services\Backup;

use App\Models\HQBackupSnapshot;
use App\Domain\HQ\Services\HQAlertService;
use Illuminate\Support\Facades\Log;

class RetentionService
{
    /**
     * Process all expired snapshots and prune them.
     */
    public function pruneExpiredSnapshots()
    {
        Log::info('RetentionService: Starting snapshot prune.');

        $expiredSnapshots = HQBackupSnapshot::whereNotNull('expires_at')
            ->where('expires_at', '<=', now())
            ->get();

        foreach ($expiredSnapshots as $snapshot) {
            try {
                // Free storage
                if ($snapshot->job && $snapshot->job->policy && $snapshot->job->policy->storageLocation) {
                    $snapshot->job->policy->storageLocation->decrement('used_bytes', $snapshot->size_bytes);
                }

                // Simulate deletion from actual storage
                $snapshot->delete();
            } catch (\Exception $e) {
                app(HQAlertService::class)->createAlert(
                    severity: 'warning',
                    title: 'retention.failed',
                    message: "Failed to prune snapshot {$snapshot->id}: {$e->getMessage()}"
                );
            }
        }
    }
}
