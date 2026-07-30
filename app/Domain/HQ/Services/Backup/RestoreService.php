<?php

namespace App\Domain\HQ\Services\Backup;

use App\Models\HQBackupSnapshot;
use App\Models\HQSystemInstance;
use App\Models\HQBackupRestoreJob;
use App\Domain\HQ\Services\HQAuditService;
use App\Domain\HQ\Services\HQAlertService;
use App\Jobs\ProcessRestoreJob;
use Illuminate\Support\Str;

class RestoreService
{
    /**
     * Start a restore job.
     */
    public function startRestore(HQBackupSnapshot $snapshot, HQSystemInstance $targetInstance, string $mode = 'execute', string $type = 'specific'): HQBackupRestoreJob
    {
        $job = HQBackupRestoreJob::create([
            'target_instance_id' => $targetInstance->id,
            'hq_backup_snapshot_id' => $snapshot->id,
            'type' => $type,
            'mode' => $mode,
            'status' => 'pending',
            'started_at' => now(),
        ]);

        app(HQAuditService::class)->logSystemAction(
            action: 'restore_started',
            category: 'system',
            severity: 'info',
            description: "Started {$mode} restore to instance {$targetInstance->name}",
            tenantId: $targetInstance->tenant_id ?? null
        );

        ProcessRestoreJob::dispatch($job);

        return $job;
    }

    /**
     * Complete a restore job.
     */
    public function completeRestore(HQBackupRestoreJob $job)
    {
        $job->update([
            'status' => 'completed',
            'completed_at' => now()
        ]);

        event(new \App\Events\BackupCompleted('restore.completed', $job->snapshot->job, "Restore completed."));

        app(HQAuditService::class)->logSystemAction(
            action: 'restore_completed',
            category: 'system',
            severity: 'info',
            description: "Completed restore job {$job->id}.",
            tenantId: $job->snapshot->job->policy->tenant_id ?? null
        );
    }

    /**
     * Fail a restore job.
     */
    public function failRestore(HQBackupRestoreJob $job, string $error)
    {
        $job->update([
            'status' => 'failed',
            'completed_at' => now(),
            'error_message' => $error
        ]);

        event(new \App\Events\BackupCompleted('restore.failed', $job->snapshot->job, "Restore failed: {$error}"));

        app(HQAlertService::class)->createAlert(
            severity: 'critical',
            title: 'restore.failed',
            message: "Restore job {$job->id} failed: {$error}",
            tenantId: $job->snapshot->job->policy->tenant_id ?? null
        );

        app(HQAuditService::class)->logSystemAction(
            action: 'restore_failed',
            category: 'system',
            severity: 'danger',
            description: "Failed restore job {$job->id}: {$error}",
            tenantId: $job->snapshot->job->policy->tenant_id ?? null
        );
    }
}
