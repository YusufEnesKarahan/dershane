<?php

namespace App\Listeners\Subscription;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Cache;

class ClearSubscriptionCaches implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(object $event): void
    {
        $subscription = $event->subscription;
        if ($subscription->branch_id) {
            Cache::tags(['tenant_' . $subscription->branch_id, 'dashboard'])->flush();
        }
    }
}
