<?php

namespace App\Domain\HQ\Services;

use App\Models\HQSystemInstance;
use App\Models\HQLicense;
use App\Models\HQCentralBackupJob;
use App\Events\SystemOfflineDetected;
use App\Events\LicenseChanged;
use App\Events\BackupFailedDetected;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class HQSchedulerService
{
    /**
     * Run all hourly scheduled checks.
     */
    public function runHourlyChecks()
    {
        Log::info('HQSchedulerService: Running hourly checks.');
        
        $this->detectOfflineSystems();
        $this->checkExpiringLicenses();
        $this->checkFailedBackups();
    }

    /**
     * Detect systems that haven't sent a heartbeat/telemetry recently.
     */
    protected function detectOfflineSystems()
    {
        // Consider offline if not seen in 15 minutes
        $threshold = now()->subMinutes(15);
        
        $offlineInstances = HQSystemInstance::where('status', 'online')
            ->where('last_seen_at', '<', $threshold)
            ->get();

        foreach ($offlineInstances as $instance) {
            $instance->update(['status' => 'offline']);
            
            $minutesOffline = now()->diffInMinutes($instance->last_seen_at);
            event(new SystemOfflineDetected($instance, $minutesOffline));
        }
    }

    /**
     * Check licenses that are expiring soon or already expired.
     */
    protected function checkExpiringLicenses()
    {
        $thirtyDaysFromNow = now()->addDays(30);

        // Notify for licenses expiring in < 30 days
        $expiringLicenses = HQLicense::where('status', 'active')
            ->whereNotNull('expires_at')
            ->where('expires_at', '<=', $thirtyDaysFromNow)
            ->where('expires_at', '>', now())
            ->get();

        foreach ($expiringLicenses as $license) {
            // We can dispatch LicenseChanged or a specific event
            event(new LicenseChanged('license.expiring', $license, [], []));
        }

        // Handle completely expired licenses
        $expiredLicenses = HQLicense::where('status', 'active')
            ->whereNotNull('expires_at')
            ->where('expires_at', '<=', now())
            ->get();
            
        foreach ($expiredLicenses as $license) {
            $license->update(['status' => 'expired']);
            event(new LicenseChanged('license.expired', $license, ['status' => 'active'], ['status' => 'expired']));
        }
    }

    /**
     * Check for backups that have failed or been stuck for a long time.
     */
    protected function checkFailedBackups()
    {
        // Example: Stuck backups that are pending for more than 2 hours
        $threshold = now()->subHours(2);
        
        // This assumes HQCentralBackupJob model exists from previous sprints
        if (class_exists(HQCentralBackupJob::class)) {
            $stuckBackups = HQCentralBackupJob::where('status', 'pending')
                ->where('created_at', '<', $threshold)
                ->get();
                
            foreach ($stuckBackups as $backup) {
                $backup->update(['status' => 'failed']);
                
                $instance = HQSystemInstance::find($backup->system_instance_id);
                event(new BackupFailedDetected($instance, [
                    'job_id' => $backup->id,
                    'message' => 'Backup timed out and was marked as failed by scheduler.'
                ]));
            }
        }
    }
}
