<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Domain\Platform\Services\HQSchedulerService;

class HQSyncCommand extends Command
{
    protected $signature = 'hq:sync';
    protected $description = 'Process pending HQ Sync events';

    public function handle(HQSchedulerService $schedulerService)
    {
        if (!config('hq.scheduler.enabled')) {
            $this->info('HQ Scheduler is currently disabled. Skipping hq:sync.');
            return 0;
        }

        $this->info('Running HQ Sync Queue Task...');
        
        $success = $schedulerService->executeTask('hq:sync', function () use ($schedulerService) {
            return $schedulerService->processSyncQueue();
        });

        if ($success) {
            $this->info('HQ Sync Queue Task completed successfully.');
            return 0;
        }

        $this->error('HQ Sync Queue Task failed.');
        return 1;
    }
}
