<?php

namespace App\Events\HQ\Billing;

use App\Models\HQSubscription;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SubscriptionUpgraded
{
    use Dispatchable, SerializesModels;

    public $subscription;

    public function __construct(HQSubscription $subscription)
    {
        $this->subscription = $subscription;
    }
}
