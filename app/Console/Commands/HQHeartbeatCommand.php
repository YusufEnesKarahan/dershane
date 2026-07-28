<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Domain\Platform\Services\HQSchedulerService;

class HQHeartbeatCommand extends Command
{
    protected $signature = 'hq:heartbeat';
    protected $description = 'Send heartbeat ping to HQ';

    public function handle(HQSchedulerService $schedulerService)
    {
        if (!config('hq.scheduler.enabled')) {
            $this->info('HQ Scheduler is currently disabled. Skipping hq:heartbeat.');
            return 0;
        }

        $this->info('Running HQ Heartbeat Task...');
        
        $success = $schedulerService->executeTask('hq:heartbeat', function () use ($schedulerService) {
            return $schedulerService->runHeartbeat();
        });

        if ($success) {
            $this->info('HQ Heartbeat Task completed successfully.');
            return 0;
        }

        $this->error('HQ Heartbeat Task failed.');
        return 1;
    }
}
