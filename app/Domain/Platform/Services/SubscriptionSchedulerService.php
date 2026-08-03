<?php

namespace App\Domain\Platform\Services;

use App\Models\Subscription;
use App\Models\License;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Events\SubscriptionExpired;
use App\Events\SubscriptionSuspended;

class SubscriptionSchedulerService
{
    /**
     * Check subscriptions that have passed their ends_at / expires_at date.
     * Transitions Active -> Grace -> Expired -> Suspended
     */
    public function processDailyChecks(): void
    {
        $this->processGracePeriod();
        $this->expireSubscriptions();
        $this->suspendExpiredSubscriptions();
    }

    protected function processGracePeriod(): void
    {
        // Find active subscriptions that have passed ends_at
        $subscriptions = Subscription::with('plan')
            ->where('status', 'active')
            ->whereNotNull('ends_at')
            ->where('ends_at', '<', Carbon::now())
            ->get();

        foreach ($subscriptions as $subscription) {
            DB::transaction(function () use ($subscription) {
                $graceDays = (int) data_get($subscription->plan?->limits, 'grace_days', 0);

                if ($graceDays > 0) {
                    $graceEndsAt = $subscription->ends_at->copy()->addDays($graceDays);
                    
                    if (Carbon::now()->isBefore($graceEndsAt)) {
                        $subscription->update([
                            'status' => 'grace',
                            'expires_at' => $graceEndsAt,
                        ]);
                        
                        \App\Models\SubscriptionHistory::create([
                            'subscription_id' => $subscription->id,
                            'action' => 'grace_started',
                            'notes' => "Subscription entered grace period until {$graceEndsAt->toDateString()}",
                        ]);
                        
                        return; // Successfully moved to grace, skip to next
                    }
                }

                // If no grace period or grace period already passed directly
                $this->markAsExpired($subscription);
            });
        }
    }

    protected function expireSubscriptions(): void
    {
        // Expire Grace or Trialing subscriptions
        $subscriptions = Subscription::with('plan')
            ->whereIn('status', ['grace', 'trialing'])
            ->whereNotNull('expires_at')
            ->where('expires_at', '<', Carbon::now())
            ->get();

        foreach ($subscriptions as $subscription) {
            DB::transaction(function () use ($subscription) {
                $this->markAsExpired($subscription);
            });
        }
    }

    protected function suspendExpiredSubscriptions(): void
    {
        // Suspend subscriptions that have been expired for more than 30 days
        $subscriptions = Subscription::where('status', 'expired')
            ->whereNotNull('expires_at')
            ->where('expires_at', '<', Carbon::now()->subDays(30))
            ->get();

        foreach ($subscriptions as $subscription) {
            DB::transaction(function () use ($subscription) {
                $subscription->update(['status' => 'suspended']);
                $subscription->license()->update(['status' => 'suspended']);
                
                \App\Models\SubscriptionHistory::create([
                    'subscription_id' => $subscription->id,
                    'action' => 'suspended',
                    'notes' => 'Subscription automatically suspended after prolonged expiration',
                ]);

                event(new SubscriptionSuspended($subscription));
            });
        }
    }

    public function markAsExpired(Subscription $subscription): void
    {
        $subscription->update(['status' => 'expired']);
        $subscription->license()->update(['status' => 'expired']);
        
        \App\Models\SubscriptionHistory::create([
            'subscription_id' => $subscription->id,
            'action' => 'expired',
            'notes' => 'Subscription automatically expired',
        ]);

        event(new SubscriptionExpired($subscription));
    }
}
