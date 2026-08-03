<?php

namespace App\Domain\Platform\Services;

use App\Core\Context\TenantContext;
use App\Models\Branch;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Subscription;
use App\Models\User;

class SubscriptionLimitService
{
    public function canAddStudent(?Branch $branch = null): bool
    {
        return $this->withinLimit($branch, 'max_students', 'students', Student::class, PHP_INT_MAX);
    }

    public function canAddUser(?Branch $branch = null): bool
    {
        return $this->withinLimit($branch, 'max_users', 'users', User::class, PHP_INT_MAX);
    }

    public function canAddTeacher(?Branch $branch = null): bool
    {
        return $this->withinLimit($branch, 'max_teachers', 'teachers', Teacher::class, PHP_INT_MAX);
    }

    public function limit(string $key, ?Branch $branch = null): ?int
    {
        $branch = $this->resolveBranch($branch);

        if (!$branch) {
            return null;
        }

        $subscription = $this->subscriptionForBranch($branch);

        if (!$this->hasUsableSubscription($subscription) || !$subscription?->plan) {
            return null;
        }

        return $subscription->plan->{$key}
            ?? data_get($subscription->plan->limits, $key)
            ?? null;
    }

    public function currentSubscription(?Branch $branch = null): ?Subscription
    {
        $branch = $this->resolveBranch($branch);

        if (!$branch) {
            return null;
        }

        return $this->subscriptionForBranch($branch);
    }

    public function resolveBranch(?Branch $branch = null): ?Branch
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

    protected function withinLimit(?Branch $branch, string $primaryKey, string $legacyKey, string $modelClass, int $default): bool
    {
        $branch = $this->resolveBranch($branch);

        if (!$branch) {
            return true;
        }

        $subscription = $this->subscriptionForBranch($branch);

        if (!$this->hasUsableSubscription($subscription) || !$subscription?->plan) {
            return false;
        }

        $limit = $subscription->plan->{$primaryKey}
            ?? data_get($subscription->plan->limits, $legacyKey)
            ?? data_get($subscription->plan->limits, $primaryKey)
            ?? $default;

        if ($limit === null || $limit === PHP_INT_MAX) {
            return true;
        }

        $query = $modelClass::withoutGlobalScopes()->where('branch_id', $branch->id);

        return $query->count() < (int) $limit;
    }

    protected function subscriptionForBranch(Branch $branch): ?Subscription
    {
        return $branch->subscription()->with('plan')->first();
    }

    protected function hasUsableSubscription(?Subscription $subscription): bool
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
}