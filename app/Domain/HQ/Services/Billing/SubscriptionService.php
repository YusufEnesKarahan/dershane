<?php

namespace App\Domain\HQ\Services\Billing;

use App\Models\HQTenant;
use App\Models\HQPlan;
use App\Models\HQSubscription;
use App\Models\HQSubscriptionItem;
use App\Events\SubscriptionCreated;
use App\Events\SubscriptionUpgraded;
use App\Events\SubscriptionCancelled;
use App\Domain\HQ\Services\HQAuditService;
use Exception;
use Illuminate\Support\Facades\DB;

class SubscriptionService
{
    protected $entitlementService;
    protected $auditService;

    public function __construct(EntitlementService $entitlementService, HQAuditService $auditService)
    {
        $this->entitlementService = $entitlementService;
        $this->auditService = $auditService;
    }

    public function subscribe(HQTenant $tenant, HQPlan $plan): HQSubscription
    {
        if ($tenant->subscriptions()->where('status', 'active')->exists()) {
            throw new Exception("Tenant already has an active subscription.");
        }

        return DB::transaction(function () use ($tenant, $plan) {
            $subscription = HQSubscription::create([
                'tenant_id' => $tenant->id,
                'plan_id' => $plan->id,
                'status' => 'active',
                'starts_at' => now(),
                'uuid' => \Illuminate\Support\Str::uuid(),
            ]);

            $this->entitlementService->syncPlanEntitlements($subscription);

            $this->auditService->logSystemAction(
                action: 'subscription_created',
                category: 'billing',
                severity: 'info',
                description: "Tenant {$tenant->id} subscribed to plan {$plan->name}."
            );

            event(new SubscriptionCreated($subscription));

            return $subscription;
        });
    }

    public function upgradeOrDowngrade(HQSubscription $subscription, HQPlan $newPlan): HQSubscription
    {
        $oldPlan = $subscription->plan;

        return DB::transaction(function () use ($subscription, $newPlan, $oldPlan) {
            $subscription->update([
                'plan_id' => $newPlan->id,
            ]);

            $this->entitlementService->syncPlanEntitlements($subscription);

            $this->auditService->logSystemAction(
                action: 'subscription_changed',
                category: 'billing',
                severity: 'info',
                description: "Subscription {$subscription->id} changed from {$oldPlan->name} to {$newPlan->name}."
            );

            event(new SubscriptionUpgraded($subscription, $oldPlan));

            return $subscription;
        });
    }

    public function cancel(HQSubscription $subscription): HQSubscription
    {
        return DB::transaction(function () use ($subscription) {
            $subscription->update([
                'status' => 'cancelled',
                'cancelled_at' => now(),
            ]);

            // Disable entitlements or keep them until billing period ends based on business logic.
            // For now, we immediately clear plan entitlements if they cancel.
            $this->entitlementService->clearPlanEntitlements($subscription->tenant_id);

            $this->auditService->logSystemAction(
                action: 'subscription_cancelled',
                category: 'billing',
                severity: 'warning',
                description: "Subscription {$subscription->id} was cancelled."
            );

            event(new SubscriptionCancelled($subscription));

            return $subscription;
        });
    }
}
