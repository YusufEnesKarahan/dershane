<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Domain\Platform\Services\SubscriptionService;

class SubscriptionCheckExpirationCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscription:check-expiration';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check and expire subscriptions that have passed their end date (Alias for billing:check-expired)';

    /**
     * Execute the console command.
     */
    public function handle(SubscriptionService $subscriptionService)
    {
        $this->info('Starting expired subscriptions check...');
        
        $expiredCount = $subscriptionService->checkExpiredSubscriptions();
        
        $this->info("Completed! Expired {$expiredCount} subscriptions.");
    }
}
