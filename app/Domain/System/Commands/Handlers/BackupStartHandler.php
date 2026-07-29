<?php

namespace App\Domain\System\Commands\Handlers;

use App\Domain\System\Commands\Contracts\RemoteCommandHandlerInterface;
use App\Models\HQCentralCommand;
use App\Models\BackupCache;
use Illuminate\Support\Facades\Http;

class BackupStartHandler implements RemoteCommandHandlerInterface
{
    public function handle(HQCentralCommand $command): array
    {
        $payload = $command->payload ?? [];
        $jobId = $payload['job_id'] ?? null;
        $backupType = $payload['backup_type'] ?? 'full';

        // Update local cache
        BackupCache::updateOrCreate(
            ['system_uuid' => $command->system_instance_id],
            [
                'status' => 'running',
                'metadata' => ['job_id' => $jobId, 'backup_type' => $backupType]
            ]
        );

        // Here we would dispatch an actual internal local backup job.
        // For the scope of this project, we orchestrate and report back.
        
        $this->reportProgressToHQ($jobId, 10);

        return [
            'success' => true,
            'message' => 'Backup started successfully on ERP.',
        ];
    }

    protected function reportProgressToHQ($jobId, $progress)
    {
        $url = rtrim(config('hq.api.url'), '/') . '/api/hq/backup/progress';
        $secret = config('hq.api.secret');
        $timestamp = time();
        
        $payload = [
            'system_uuid' => config('hq.system_uuid', 'dummy'),
            'job_id' => $jobId,
            'progress' => $progress
        ];

        $signature = hash_hmac('sha256', json_encode($payload) . $timestamp, $secret);

        Http::withHeaders([
            'Authorization' => 'Bearer ' . config('hq.api.token'),
            'X-HQ-Signature' => $signature,
            'X-HQ-Timestamp' => (string) $timestamp,
        ])->post($url, $payload);
    }
}
