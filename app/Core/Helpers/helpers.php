<?php

declare(strict_types=1);

use App\Core\Support\Facades\Feature;
use Illuminate\Support\Facades\Config;

if (! function_exists('feature_active')) {
    /**
     * Helper to quickly check if a SaaS feature flag is active.
     */
    function feature_active(string $feature): bool
    {
        return Feature::isActive($feature);
    }
}

if (! function_exists('brand_setting')) {
    /**
     * Helper to retrieve branding settings dynamically.
     */
    function brand_setting(string $key, mixed $default = null): mixed
    {
        return Config::get("brand.{$key}", $default);
    }
}
