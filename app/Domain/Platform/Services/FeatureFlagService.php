<?php

namespace App\Domain\Platform\Services;

use App\Models\FeatureFlag;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class FeatureFlagService
{
    private const CACHE_TTL = 3600; // 60 minutes
    private const CACHE_KEY_PREFIX = 'feature_flag:';

    /**
     * Check if a feature flag is enabled.
     *
     * Usage: app(FeatureFlagService::class)->enabled('advanced_reports')
     */
    public function enabled(string $feature): bool
    {
        return Cache::remember(
            self::CACHE_KEY_PREFIX . $feature,
            self::CACHE_TTL,
            function () use ($feature) {
                $flag = FeatureFlag::where('name', $feature)->first();
                return $flag ? $flag->enabled : false;
            }
        );
    }

    /**
     * Check if a feature flag is disabled.
     */
    public function disabled(string $feature): bool
    {
        return !$this->enabled($feature);
    }

    /**
     * Get all feature flags.
     */
    public function getAllFlags(): Collection
    {
        return Cache::remember('feature_flags:all', self::CACHE_TTL, function () {
            return FeatureFlag::all();
        });
    }

    /**
     * Clear the cache for a specific feature flag or all flags.
     */
    public function clearCache(?string $feature = null): void
    {
        if ($feature) {
            Cache::forget(self::CACHE_KEY_PREFIX . $feature);
        }

        Cache::forget('feature_flags:all');
    }
}
