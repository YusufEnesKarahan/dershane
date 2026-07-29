<?php

namespace App\Domain\HQ\Services;

use App\Models\HQBackupPolicy;
use App\Models\HQBackupJob;
use App\Models\HQBackupLog;
use App\Models\HQSystemInstance;
use App\Domain\HQ\Enums\HQCommandType;
use Illuminate\Support\Facades\DB;

class HQBackupService
{
    public function __construct(
        protected HQRemoteCommandService $remoteCommandService
    ) {}

    public function createPolicy(array $data): HQBackupPolicy
    {
        return DB::transaction(function () use ($data) {
            return HQBackupPolicy::create([
                'tenant_id' => $data['tenant_id'] ?? null,
                'system_instance_id' => $data['system_instance_id'] ?? null,
                'name' => $data['name'],
                'frequency' => $data['frequency'],
                'retention_days' => $data['retention_days'] ?? 7,
                'backup_type' => $data['backup_type'],
                'is_active' => $data['is_active'] ?? true,
            ]);
        });
    }

    public function dispatchBackup(HQSystemInstance $instance, HQBackupPolicy $policy): HQBackupJob
    {
        return DB::transaction(function () use ($instance, $policy) {
            $job = HQBackupJob::create([
                'backup_policy_id' => $policy->id,
                'system_instance_id' => $instance->id,
                'status' => 'pending',
            ]);

            $this->logJob($job, 'dispatch', ['message' => 'Job dispatched']);

            $this->remoteCommandService->dispatchCommand($instance, HQCommandType::BACKUP_START, [
                'job_id' => $job->id,
                'backup_type' => $policy->backup_type,
            ]);

            return $job;
        });
    }

    public function retryFailedBackup(HQBackupJob $job): HQBackupJob
    {
        if ($job->status !== 'failed') {
            throw new \Exception("Only failed jobs can be retried.");
        }

        return DB::transaction(function () use ($job) {
            $job->update([
                'status' => 'pending',
                'error_message' => null,
            ]);

            $this->logJob($job, 'retry', ['message' => 'Job retried']);

            $this->remoteCommandService->dispatchCommand($job->systemInstance, HQCommandType::BACKUP_START, [
                'job_id' => $job->id,
                'backup_type' => $job->policy->backup_type,
            ]);

            return $job;
        });
    }

    public function cleanupExpiredBackups()
    {
        // Simple logic for orchestration cleanup
        $policies = HQBackupPolicy::where('is_active', true)->get();

        foreach ($policies as $policy) {
            $expiredDate = now()->subDays($policy->retention_days);
            $expiredJobs = HQBackupJob::where('backup_policy_id', $policy->id)
                ->where('status', 'completed')
                ->where('finished_at', '<', $expiredDate)
                ->get();

            foreach ($expiredJobs as $job) {
                // Here we could dispatch a remote command to delete the backup file.
                // For now, we just mark or delete the job record.
                $job->delete();
            }
        }
    }

    public function logJob(HQBackupJob $job, string $action, array $payload = null, array $response = null)
    {
        HQBackupLog::create([
            'backup_job_id' => $job->id,
            'action' => $action,
            'payload' => $payload,
            'response' => $response,
        ]);
    }
}
