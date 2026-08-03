<?php

namespace App\Domain\Platform\Services;

use App\Core\Context\TenantContext;
use App\Models\Branch;
use App\Models\Plan;
use App\Models\Subscription;
use Illuminate\Support\Str;

class FeatureGateService
{
    public function currentBranch(): ?Branch
    {
        return $this->resolveBranch();
    }

    public function currentSubscription(?Branch $branch = null): ?Subscription
    {
        $branch = $this->resolveBranch($branch);

        if (!$branch) {
            return null;
        }

        return $branch->subscription()->with('plan')->first();
    }

    public function currentPlan(?Branch $branch = null): ?Plan
    {
        return $this->currentSubscription($branch)?->plan;
    }

    public function can(string $feature, ?Branch $branch = null): bool
    {
        $subscription = $this->currentSubscription($branch);

        if (!$this->isUsableSubscription($subscription) || !$subscription?->plan) {
            return false;
        }

        $featureKey = $this->normalizeFeature($feature);

        foreach (($subscription->plan->features ?? []) as $enabledFeature) {
            if ($this->normalizeFeature((string) $enabledFeature) === $featureKey) {
                return true;
            }
        }

        return false;
    }

    public function features(?Branch $branch = null): array
    {
        return $this->currentPlan($branch)?->features ?? [];
    }

    protected function resolveBranch(?Branch $branch = null): ?Branch
    {
        if ($branch) {
            return $branch;
        }

        $branchId = TenantContext::getActiveBranchId();

        if (!$branchId && auth()->check()) {
            $branchId = auth()->user()->branch_id;
        }

        if (!$branchId) {
            return null;
        }

        return Branch::query()->find($branchId);
    }

    protected function isUsableSubscription(?Subscription $subscription): bool
    {
        if (!$subscription) {
            return false;
        }

        if (!in_array($subscription->status, ['active', 'trial', 'trialing'], true)) {
            return false;
        }

        if ($subscription->expires_at && $subscription->expires_at->isPast()) {
            return false;
        }

        if ($subscription->trial_ends_at && $subscription->trial_ends_at->isPast()) {
            return false;
        }

        return true;
    }

    protected function normalizeFeature(string $feature): string
    {
        return Str::of($feature)
            ->trim()
            ->lower()
            ->replace(['-', ' '], '_')
            ->value();
    }
}