<?php

namespace App\Domain\Platform\Services;

use App\Models\License;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\SubscriptionLog;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SubscriptionService
{
    /**
     * Start a trial subscription for the license.
     */
    public function startTrial(License $license, Plan $plan, int $days = 14): Subscription
    {
        return DB::transaction(function () use ($license, $plan, $days) {
            $now = Carbon::now();
            $trialEndsAt = $now->copy()->addDays($days);

            // Update license
            $license->update([
                'status' => 'trial',
                'plan' => $plan->slug,
                'plan_id' => $plan->id,
                'starts_at' => $now,
                'trial_ends_at' => $trialEndsAt,
                'expires_at' => clone $trialEndsAt,
            ]);

            // Create subscription
            $subscription = Subscription::create([
                'license_id' => $license->id,
                'branch_id' => $license->branch_id,
                'plan_id' => $plan->id,
                'status' => 'trialing',
                'starts_at' => $now,
                'trial_ends_at' => $trialEndsAt,
                'ends_at' => clone $trialEndsAt,
                'price' => $plan->price,
            ]);

            SubscriptionLog::create([
                'subscription_id' => $subscription->id,
                'action' => 'trial_started',
                'new_plan_id' => $plan->id,
                'notes' => "Started {$days}-day trial",
            ]);

            return $subscription;
        });
    }

    /**
     * Activate a subscription (convert from trial to active).
     */
    public function activateSubscription(License $license, ?Plan $plan = null, int $months = 1): ?Subscription
    {
        return DB::transaction(function () use ($license, $plan, $months) {
            $subscription = $license->subscription;
            if (!$subscription) {
                return null;
            }

            $activePlan = $plan ?? $subscription->plan;
            $now = Carbon::now();
            $endsAt = $now->copy()->addMonths($months);

            // Update license
            $license->update([
                'status' => 'active',
                'plan' => $activePlan->slug,
                'plan_id' => $activePlan->id,
                'trial_ends_at' => null, // Trial over
                'expires_at' => $endsAt,
            ]);

            // Update subscription
            $subscription->update([
                'status' => 'active',
                'plan_id' => $activePlan->id,
                'trial_ends_at' => null,
                'ends_at' => $endsAt,
                'price' => $activePlan->price,
            ]);

            SubscriptionLog::create([
                'subscription_id' => $subscription->id,
                'action' => 'activated',
                'new_plan_id' => $activePlan->id,
                'notes' => 'Subscription activated',
            ]);

            return $subscription;
        });
    }

    /**
     * Upgrade or downgrade the subscription plan.
     */
    public function changePlan(License $license, Plan $newPlan): ?Subscription
    {
        return DB::transaction(function () use ($license, $newPlan) {
            $subscription = $license->subscription;
            if (!$subscription) {
                return null;
            }

            $oldPlanId = $subscription->plan_id;
            $action = ($newPlan->price > $subscription->price) ? 'upgraded' : 'downgraded';

            // Update license
            $license->update([
                'plan' => $newPlan->slug,
                'plan_id' => $newPlan->id,
            ]);

            // Update subscription
            $subscription->update([
                'plan_id' => $newPlan->id,
                'price' => $newPlan->price,
            ]);

            SubscriptionLog::create([
                'subscription_id' => $subscription->id,
                'action' => $action,
                'old_plan_id' => $oldPlanId,
                'new_plan_id' => $newPlan->id,
                'notes' => "Plan changed from {$oldPlanId} to {$newPlan->id}",
            ]);

            return $subscription;
        });
    }

    /**
     * Mark expired subscriptions.
     */
    public function checkExpiredSubscriptions(): int
    {
        $expiredCount = 0;
        
        $expiredSubscriptions = Subscription::whereIn('status', ['trialing', 'active'])
            ->whereNotNull('ends_at')
            ->where('ends_at', '<', Carbon::now())
            ->get();

        foreach ($expiredSubscriptions as $subscription) {
            DB::transaction(function () use ($subscription, &$expiredCount) {
                $subscription->update(['status' => 'expired']);
                $subscription->license()->update(['status' => 'expired']);

                SubscriptionLog::create([
                    'subscription_id' => $subscription->id,
                    'action' => 'expired',
                    'notes' => 'Subscription automatically expired',
                ]);

                $expiredCount++;
            });
        }

        return $expiredCount;
    }
}
