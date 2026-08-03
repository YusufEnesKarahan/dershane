<?php

namespace App\Listeners\Subscription;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\PlatformAuditLog;
use App\Models\SubscriptionHistory;

class LogSubscriptionHistory implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(object $event): void
    {
        $subscription = $event->subscription;
        $branch = $subscription->branch;
        $eventName = class_basename($event);
        
        $action = match ($eventName) {
            'SubscriptionCreated' => 'created',
            'SubscriptionActivated' => 'activated',
            'SubscriptionRenewed' => 'renewed',
            'SubscriptionExpired' => 'expired',
            'SubscriptionSuspended' => 'suspended',
            'SubscriptionReactivated' => 'reactivated',
            'SubscriptionCancelled' => 'cancelled',
            default => 'updated',
        };

        if ($branch) {
            PlatformAuditLog::record(
                auth()->check() ? auth()->user() : null,
                "subscription.{$action}",
                $branch,
                [
                    'description' => "Subscription {$action}",
                    'subscription_id' => $subscription->id,
                    'plan' => $subscription->plan?->name,
                ]
            );
        }
    }
}
