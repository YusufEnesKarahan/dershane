<?php

namespace App\Support;

use App\Domain\Platform\Services\FeatureGateService;
use App\Domain\Platform\Services\SubscriptionLimitService;
use App\Models\Branch;
use App\Models\Plan;
use App\Models\Subscription;

class SaaS
{
    public static function currentBranch(): ?Branch
    {
        return app(FeatureGateService::class)->currentBranch();
    }

    public static function currentSubscription(?Branch $branch = null): ?Subscription
    {
        return app(FeatureGateService::class)->currentSubscription($branch);
    }

    public static function currentPlan(?Branch $branch = null): ?Plan
    {
        return app(FeatureGateService::class)->currentPlan($branch);
    }

    public static function can(string $feature, ?Branch $branch = null): bool
    {
        return app(FeatureGateService::class)->can($feature, $branch);
    }

    public static function limit(string $key, ?Branch $branch = null): ?int
    {
        return app(SubscriptionLimitService::class)->limit($key, $branch);
    }

    public static function features(?Branch $branch = null): array
    {
        return app(FeatureGateService::class)->features($branch);
    }
}