<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\HQBackupRestoreJob;
use App\Domain\HQ\Services\Backup\RestoreService;

class ProcessRestoreJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $job;

    /**
     * Create a new job instance.
     */
    public function __construct(HQBackupRestoreJob $job)
    {
        $this->job = $job;
    }

    /**
     * Execute the job.
     */
    public function handle(RestoreService $service): void
    {
        if ($this->job->status !== 'pending') {
            return;
        }

        try {
            $this->job->update(['status' => 'running']);

            // Simulate the restore process execution
            // Validation Mode
            if ($this->job->mode === 'validation' || $this->job->mode === 'dry_run') {
                // Perform checks only
            } else {
                // Execute actual data transfer
            }

            $service->completeRestore($this->job);

        } catch (\Exception $e) {
            $service->failRestore($this->job, $e->getMessage());
        }
    }
}
