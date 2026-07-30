<?php

namespace App\Domain\HQ\Services\Backup;

use App\Models\HQBackupJob;
use App\Models\HQBackupRestoreJob;
use App\Models\HQBackupSnapshot;
use App\Models\HQBackupStorageLocation;

class BackupHealthService
{
    /**
     * Get health metrics for dashboard.
     */
    public function getMetrics(): array
    {
        return [
            'policies' => \App\Models\HQBackupPolicy::count(),
            'successful' => HQBackupJob::where('status', 'completed')->count(),
            'failed' => HQBackupJob::where('status', 'failed')->count(),
            'storage' => HQBackupStorageLocation::sum('used_bytes'),
            
            // These are for the specific Backup Index dashboard
            'successful_backups' => HQBackupJob::where('status', 'completed')->count(),
            'failed_backups' => HQBackupJob::where('status', 'failed')->count(),
            'running_jobs' => HQBackupJob::where('status', 'pending')->count(),
            'latest_snapshot' => HQBackupSnapshot::latest()->first(),
            'storage_usage' => HQBackupStorageLocation::sum('used_bytes'),
            'total_capacity' => HQBackupStorageLocation::sum('capacity_bytes'),
            'restore_success' => HQBackupRestoreJob::where('status', 'completed')->count(),
            'restore_failures' => HQBackupRestoreJob::where('status', 'failed')->count(),
        ];
    }
}
