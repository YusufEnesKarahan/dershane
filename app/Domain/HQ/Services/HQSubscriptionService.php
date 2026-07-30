<?php

namespace App\Domain\HQ\Services;

use App\Models\HQSubscription;
use App\Models\HQSubscriptionPlan;
use App\Models\HQTenant;
use App\Models\HQSubscriptionHistory;
use App\Events\HQ\Billing\SubscriptionCreated;
use App\Events\HQ\Billing\SubscriptionUpgraded;
use App\Events\HQ\Billing\SubscriptionCancelled;
use App\Events\HQ\Billing\SubscriptionExpired;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class HQSubscriptionService
{
    /**
     * Create a new subscription for a tenant.
     */
    public function createSubscription(HQTenant $tenant, HQSubscriptionPlan $plan): HQSubscription
    {
        return DB::transaction(function () use ($tenant, $plan) {
            // Cancel any active subscriptions
            $activeSubscriptions = $tenant->subscriptions()->active()->get();
            foreach ($activeSubscriptions as $activeSub) {
                $this->cancelSubscription($activeSub, 'Replaced by new subscription');
            }

            $startsAt = now();
            $endsAt = $plan->billing_period === 'yearly' ? now()->addYear() : now()->addMonth();

            $subscription = HQSubscription::create([
                'tenant_id' => $tenant->id,
                'plan_id' => $plan->id,
                'status' => 'active',
                'starts_at' => $startsAt,
                'ends_at' => $endsAt,
            ]);

            HQSubscriptionHistory::create([
                'subscription_id' => $subscription->id,
                'new_plan_id' => $plan->id,
                'action' => 'created',
            ]);

            event(new SubscriptionCreated($subscription));

            return $subscription;
        });
    }

    /**
     * Change an existing subscription to a new plan (upgrade/downgrade).
     */
    public function changePlan(HQSubscription $subscription, HQSubscriptionPlan $newPlan): HQSubscription
    {
        return DB::transaction(function () use ($subscription, $newPlan) {
            $oldPlanId = $subscription->plan_id;
            
            $subscription->update([
                'plan_id' => $newPlan->id,
            ]);

            HQSubscriptionHistory::create([
                'subscription_id' => $subscription->id,
                'old_plan_id' => $oldPlanId,
                'new_plan_id' => $newPlan->id,
                'action' => 'upgraded', // Simplified: Could determine up/down based on price
            ]);

            event(new SubscriptionUpgraded($subscription));

            return $subscription;
        });
    }

    /**
     * Cancel an active subscription.
     */
    public function cancelSubscription(HQSubscription $subscription, string $reason = 'User requested'): bool
    {
        return DB::transaction(function () use ($subscription, $reason) {
            $subscription->update([
                'status' => 'cancelled',
                'cancelled_at' => now(),
                'metadata' => array_merge($subscription->metadata ?? [], ['cancel_reason' => $reason]),
            ]);

            HQSubscriptionHistory::create([
                'subscription_id' => $subscription->id,
                'old_plan_id' => $subscription->plan_id,
                'action' => 'cancelled',
            ]);

            event(new SubscriptionCancelled($subscription));

            return true;
        });
    }

    /**
     * Renew an active or past due subscription.
     */
    public function renewSubscription(HQSubscription $subscription): HQSubscription
    {
        return DB::transaction(function () use ($subscription) {
            $plan = $subscription->plan;
            $endsAt = $plan->billing_period === 'yearly' ? now()->addYear() : now()->addMonth();

            $subscription->update([
                'status' => 'active',
                'ends_at' => $endsAt,
            ]);

            HQSubscriptionHistory::create([
                'subscription_id' => $subscription->id,
                'new_plan_id' => $plan->id,
                'action' => 'renewed',
            ]);

            return $subscription;
        });
    }

    /**
     * Mark a subscription as expired.
     */
    public function expireSubscription(HQSubscription $subscription): bool
    {
        return DB::transaction(function () use ($subscription) {
            $subscription->update([
                'status' => 'expired',
            ]);

            event(new SubscriptionExpired($subscription));

            return true;
        });
    }

    /**
     * Get the active subscription for a tenant.
     */
    public function getActiveSubscription(HQTenant $tenant): ?HQSubscription
    {
        return $tenant->subscriptions()->active()->latest('starts_at')->first();
    }
}
