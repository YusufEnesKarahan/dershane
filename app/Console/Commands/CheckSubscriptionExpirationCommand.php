<?php

namespace App\Console\Commands;

use App\Domain\Platform\Services\SubscriptionManagementService;
use Illuminate\Console\Command;

class CheckSubscriptionExpirationCommand extends Command
{
    protected $signature = 'subscription:check-expiration';

    protected $description = 'Expire tenant subscriptions that passed their end date';

    public function handle(SubscriptionManagementService $subscriptionService): int
    {
        $this->info('Starting subscription expiration check...');

        $expiredCount = $subscriptionService->checkExpiredSubscriptions();

        $this->info("Completed! Expired {$expiredCount} subscriptions.");

        return self::SUCCESS;
    }
}