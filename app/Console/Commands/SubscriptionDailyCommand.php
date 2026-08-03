<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Domain\Platform\Services\SubscriptionSchedulerService;

class SubscriptionDailyCommand extends Command
{
    protected $signature = 'subscription:daily';
    protected $description = 'Process daily subscription lifecycle checks (Grace, Expiration, Suspension)';

    public function handle(SubscriptionSchedulerService $scheduler): void
    {
        $this->info('Starting daily subscription checks...');
        $scheduler->processDailyChecks();
        $this->info('Daily subscription checks completed successfully.');
    }
}
