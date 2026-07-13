<?php

declare(strict_types=1);

namespace App\Core\Services;

use Illuminate\Support\Facades\Config;

class FeatureManager
{
    /**
     * Check if a feature is enabled.
     */
    public function isEnabled(string $feature): bool
    {
        return (bool) Config::get("features.flags.{$feature}", false);
    }

    /**
     * Get all feature flags.
     *
     * @return array<string, bool>
     */
    public function all(): array
    {
        return Config::get('features.flags', []);
    }
}
