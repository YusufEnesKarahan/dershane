<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Domain\Platform\Services\SubscriptionSchedulerService;

class SubscriptionHourlyCommand extends Command
{
    protected $signature = 'subscription:hourly';
    protected $description = 'Process hourly subscription checks';

    public function handle(SubscriptionSchedulerService $scheduler): void
    {
        $this->info('Starting hourly subscription checks...');
        // For urgent/hourly tasks. For now, daily checks cover most lifecycle,
        // but we can call it here as well if needed to act immediately on hour boundaries.
        $scheduler->processDailyChecks(); 
        $this->info('Hourly checks completed.');
    }
}
