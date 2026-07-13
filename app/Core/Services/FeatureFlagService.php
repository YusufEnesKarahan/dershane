<?php

declare(strict_types=1);

namespace App\Core\Services;

use Illuminate\Support\Facades\Config;

class FeatureFlagService
{
    /**
     * Determine if the given feature flag is active.
     */
    public function isActive(string $feature): bool
    {
        $version = Config::get('features.version', 1);
        $packages = Config::get('features.packages', []);

        // Check if feature is defined in the package of the active version
        if (isset($packages[$version]['features'][$feature])) {
            return (bool) $packages[$version]['features'][$feature];
        }

        // Fallback to global flags override
        $globalFlags = Config::get('features.flags', []);
        if (isset($globalFlags[$feature])) {
            return (bool) $globalFlags[$feature];
        }

        return false;
    }

    /**
     * Get the name of the currently active SaaS version.
     */
    public function getVersionName(): string
    {
        $version = Config::get('features.version', 1);

        return Config::get("features.packages.{$version}.name", 'Unknown Sürüm');
    }
}
