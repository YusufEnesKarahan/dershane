<?php

namespace App\Domain\HQ\Services;

use App\Models\HQTenant;
use App\Models\HQSubscription;

class HQEntitlementService
{
    /**
     * Check if a tenant has access to a specific feature.
     */
    public function canUseFeature(HQTenant $tenant, string $featureKey): bool
    {
        $subscription = app(HQSubscriptionService::class)->getActiveSubscription($tenant);

        if (!$subscription || !$subscription->plan) {
            return false;
        }

        $features = $subscription->plan->features ?? [];
        
        return in_array($featureKey, $features);
    }

    /**
     * Get all limits for a tenant.
     */
    public function getLimits(HQTenant $tenant): array
    {
        $subscription = app(HQSubscriptionService::class)->getActiveSubscription($tenant);

        if (!$subscription || !$subscription->plan) {
            return [];
        }

        return $subscription->plan->limits ?? [];
    }

    /**
     * Check if a specific quota is within limits.
     * Example: checkQuota($tenant, 'students', 501) -> false if limit is 500
     */
    public function checkQuota(HQTenant $tenant, string $limitKey, int $currentUsage): bool
    {
        $limits = $this->getLimits($tenant);

        if (!isset($limits[$limitKey])) {
            // If the limit is not explicitly defined, we might assume unlimited or zero.
            // Assuming unlimited if not defined in the plan limits for a specific key.
            // But if the tenant has no subscription at all, getLimits returns [] and they might get unlimited?
            // Let's assume if there's no subscription, they have 0 limit.
            $subscription = app(HQSubscriptionService::class)->getActiveSubscription($tenant);
            if (!$subscription) {
                return false;
            }
            return true;
        }

        $limitValue = $limits[$limitKey];
        
        // Handle string limits like "50GB" if needed, but for integers:
        if (is_numeric($limitValue)) {
            return $currentUsage <= (int)$limitValue;
        }

        return true;
    }
}
