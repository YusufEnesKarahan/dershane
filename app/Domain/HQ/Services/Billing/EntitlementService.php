<?php

namespace App\Domain\HQ\Services\Billing;

use App\Models\HQTenant;
use App\Models\HQSubscription;
use App\Models\HQEntitlement;
use Illuminate\Support\Facades\DB;

class EntitlementService
{
    /**
     * Check if tenant has access to a specific feature or extension.
     */
    public function hasAccess(HQTenant $tenant, string $featureKey): bool
    {
        return HQEntitlement::where('tenant_id', $tenant->id)
            ->where('feature_key', $featureKey)
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->exists();
    }

    /**
     * Get the current limit for a specific feature for a tenant.
     */
    public function getLimit(HQTenant $tenant, string $featureKey)
    {
        $entitlement = HQEntitlement::where('tenant_id', $tenant->id)
            ->where('feature_key', $featureKey)
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->first();

        return $entitlement ? $entitlement->limit_value : null;
    }

    /**
     * Sync entitlements based on a subscription's plan.
     */
    public function syncPlanEntitlements(HQSubscription $subscription)
    {
        $tenantId = $subscription->tenant_id;
        $plan = $subscription->plan;

        DB::transaction(function () use ($tenantId, $plan) {
            // Clear existing plan entitlements
            HQEntitlement::where('tenant_id', $tenantId)->where('source', 'plan')->delete();

            // Add features
            if (is_array($plan->features)) {
                foreach ($plan->features as $featureKey) {
                    HQEntitlement::create([
                        'tenant_id' => $tenantId,
                        'feature_key' => $featureKey,
                        'limit_value' => 'true',
                        'source' => 'plan',
                    ]);
                }
            }

            // Add limits
            if (is_array($plan->limits)) {
                foreach ($plan->limits as $featureKey => $limitValue) {
                    HQEntitlement::create([
                        'tenant_id' => $tenantId,
                        'feature_key' => $featureKey,
                        'limit_value' => (string) $limitValue,
                        'source' => 'plan',
                    ]);
                }
            }
        });
    }

    public function clearPlanEntitlements(int $tenantId)
    {
        HQEntitlement::where('tenant_id', $tenantId)->where('source', 'plan')->delete();
    }
}
