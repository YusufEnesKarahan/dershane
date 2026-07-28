<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Domain\Platform\Services\HQSchedulerService;

class HQTelemetryCommand extends Command
{
    protected $signature = 'hq:telemetry';
    protected $description = 'Send telemetry snapshot to HQ';

    public function handle(HQSchedulerService $schedulerService)
    {
        if (!config('hq.scheduler.enabled')) {
            $this->info('HQ Scheduler is currently disabled. Skipping hq:telemetry.');
            return 0;
        }

        $this->info('Running HQ Telemetry Task...');
        
        $success = $schedulerService->executeTask('hq:telemetry', function () use ($schedulerService) {
            return $schedulerService->runTelemetry();
        });

        if ($success) {
            $this->info('HQ Telemetry Task completed successfully.');
            return 0;
        }

        $this->error('HQ Telemetry Task failed.');
        return 1;
    }
}
