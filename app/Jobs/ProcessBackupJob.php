<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\HQBackupJob;
use App\Core\Services\Backup\BackupOrchestrationService;

class ProcessBackupJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $job;

    /**
     * Create a new job instance.
     */
    public function __construct(HQBackupJob $job)
    {
        $this->job = $job;
    }

    /**
     * Execute the job.
     */
    public function handle(BackupOrchestrationService $service): void
    {
        if ($this->job->status !== 'pending') {
            return;
        }

        try {
            $this->job->update(['status' => 'running']);

            // Simulate the actual backup process execution (e.g. dump DB, zip files)
            // sleep(1);

            // Generate mock path and size
            $path = 's3://bucket/backups/' . $this->job->uuid . '.zip';
            $size = rand(1024 * 1024 * 10, 1024 * 1024 * 500); // 10MB to 500MB

            $snapshotType = $this->job->policy->backup_type ?? 'full';

            $service->completeBackup($this->job, $size, $path, $snapshotType);

        } catch (\Exception $e) {
            $service->failBackup($this->job, $e->getMessage());
        }
    }
}
