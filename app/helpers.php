<?php

use App\Domain\Package\Services\PackageService;

if (!function_exists('feature_enabled')) {
    /**
     * Check if a feature is enabled for the current or specified branch package.
     */
    function feature_enabled(string $code, $branch = null): bool
    {
        return app(PackageService::class)->hasFeature($branch, $code);
    }
}
