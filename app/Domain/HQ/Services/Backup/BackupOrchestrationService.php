<?php

namespace App\Domain\HQ\Services\Backup;

use App\Models\HQBackupPolicy;
use App\Models\HQBackupJob;
use App\Models\HQTenant;
use App\Models\HQSystemInstance;
use App\Domain\HQ\Services\HQEntitlementService;
use App\Domain\HQ\Services\HQAlertService;
use App\Domain\HQ\Services\HQAuditService;
use App\Jobs\ProcessBackupJob;

class BackupOrchestrationService
{
    /**
     * Start a backup job based on a policy.
     */
    public function startBackup(HQBackupPolicy $policy, ?HQSystemInstance $targetInstance = null): HQBackupJob
    {
        $tenant = $policy->tenant;
        
        // 1. Billing & Entitlement Check
        $entitlementService = app(HQEntitlementService::class);
        $totalStorageBytes = \App\Models\HQBackupSnapshot::whereHas('job.policy', function ($q) use ($tenant) {
            $q->where('tenant_id', $tenant->id);
        })->sum('size_bytes');
        
        $storageGB = $totalStorageBytes / (1024 * 1024 * 1024);
        
        if (!$entitlementService->checkQuota($tenant, 'max_backup_storage_gb', $storageGB)) {
            app(HQAlertService::class)->createAlert(
                severity: 'warning',
                title: 'storage.limit_reached',
                message: "Tenant {$tenant->name} has reached their backup storage limit.",
                tenantId: $tenant->id
            );
            throw new \Exception("Backup failed: Storage quota exceeded.");
        }

        // 2. Create Job
        $job = HQBackupJob::create([
            'backup_policy_id' => $policy->id,
            'system_instance_id' => $targetInstance ? $targetInstance->id : $policy->system_instance_id,
            'status' => 'pending',
            'started_at' => now(),
            'storage_location' => $policy->storageLocation->name ?? 'default',
        ]);

        // 3. Audit Log
        app(HQAuditService::class)->logSystemAction(
            action: 'backup_started',
            category: 'system',
            severity: 'info',
            description: "Started backup job {$job->id} for policy {$policy->name}",
            tenantId: $tenant->id
        );

        // 4. Dispatch Job to Queue
        ProcessBackupJob::dispatch($job);

        return $job;
    }

    /**
     * Complete a backup job.
     */
    public function completeBackup(HQBackupJob $job, int $sizeBytes, string $path, string $snapshotType = 'full')
    {
        $job->update([
            'status' => 'completed',
            'finished_at' => now(),
            'size' => $sizeBytes
        ]);

        // 1. Snapshot engine creates snapshot record
        app(SnapshotService::class)->registerSnapshot($job, $snapshotType, $path, $sizeBytes);

        // 2. Update storage usage
        if ($job->policy && $job->policy->storageLocation) {
            $job->policy->storageLocation->increment('used_bytes', $sizeBytes);
        }

        // 3. Fire workflow event
        event(new \App\Events\BackupCompleted('backup.completed', $job, 'Backup finished successfully.'));

        // 4. Audit Log
        if ($job->policy) {
            app(HQAuditService::class)->logSystemAction(
                action: 'backup_completed',
                category: 'system',
                severity: 'info',
                description: "Completed backup job {$job->id}. Size: {$sizeBytes} bytes.",
                tenantId: $job->policy->tenant_id
            );
        }
    }

    /**
     * Fail a backup job.
     */
    public function failBackup(HQBackupJob $job, string $error)
    {
        $job->update([
            'status' => 'failed',
            'finished_at' => now(),
            'error_message' => $error
        ]);

        // 1. Alert
        $tenantId = $job->policy ? $job->policy->tenant_id : null;
        app(HQAlertService::class)->createAlert(
            severity: 'critical',
            title: 'backup.failed',
            message: "Backup job {$job->id} failed: {$error}",
            tenantId: $tenantId
        );

        // 2. Event
        event(new \App\Events\BackupCompleted('backup.failed', $job, "Backup job failed: {$error}"));

        // 3. Audit Log
        if ($job->policy) {
            app(HQAuditService::class)->logSystemAction(
                action: 'backup_failed',
                category: 'system',
                severity: 'danger',
                description: "Failed backup job {$job->id}: {$error}",
                tenantId: $job->policy->tenant_id
            );
        }
    }
}
