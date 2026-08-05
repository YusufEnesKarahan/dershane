<?php

namespace App\Domain\Subscription\Services;

use App\Models\Branch;
use App\Models\Subscription;
use App\Models\Plan;
use App\Core\Context\TenantContext;

class SubscriptionService
{
    protected function resolveBranchId(Branch|int|null $tenant = null): ?int
    {
        if ($tenant instanceof Branch) {
            return $tenant->id;
        }

        if (is_numeric($tenant) && $tenant > 0) {
            return (int) $tenant;
        }

        return TenantContext::getActiveBranchId()
            ?? session('active_branch_id')
            ?? auth()->user()?->branch_id
            ?? Branch::value('id');
    }

    public function getSubscription(Branch|int|null $tenant = null): ?Subscription
    {
        $branchId = $this->resolveBranchId($tenant);

        if (!$branchId) {
            return null;
        }

        return Subscription::where('branch_id', $branchId)
            ->with('plan')
            ->orderBy('id', 'desc')
            ->first();
    }

    public function isExpired(Branch|int|null $tenant = null): bool
    {
        $subscription = $this->getSubscription($tenant);

        if (!$subscription) {
            return false;
        }

        $expiry = $subscription->expires_at ?? $subscription->ends_at;

        if ($expiry && $expiry->isPast()) {
            return true;
        }

        return in_array($subscription->status, ['expired', 'cancelled', 'suspended'], true);
    }

    public function hasFeature(Branch|int|null $tenant = null, string $featureKey): bool
    {
        $subscription = $this->getSubscription($tenant);

        if (!$subscription || !$subscription->plan) {
            return true; // Default fallback to allow access if no plan is bound
        }

        $plan = $subscription->plan;
        $features = $plan->features ?? [];

        if (is_array($features)) {
            if (isset($features[$featureKey])) {
                return (bool) $features[$featureKey];
            }

            if (in_array($featureKey, $features, true)) {
                return true;
            }
        }

        return true;
    }

    public function checkLimit(Branch|int|null $tenant = null, string $limitKey): bool
    {
        $subscription = $this->getSubscription($tenant);

        if (!$subscription || !$subscription->plan) {
            return true;
        }

        $plan = $subscription->plan;
        $limits = $plan->limits ?? [];

        $limit = $limits[$limitKey] ?? null;

        if ($limit === null || $limit === 'unlimited' || (int) $limit <= 0) {
            return true;
        }

        return true;
    }

    public function renew(Subscription $subscription, int $days = 30): Subscription
    {
        $currentExpiry = $subscription->expires_at ?? $subscription->ends_at ?? now();
        $baseDate = $currentExpiry->isPast() ? now() : $currentExpiry;

        $newExpiry = $baseDate->copy()->addDays($days);

        $subscription->update([
            'status' => 'active',
            'starts_at' => now(),
            'ends_at' => $newExpiry,
            'expires_at' => $newExpiry,
        ]);

        return $subscription;
    }
}
