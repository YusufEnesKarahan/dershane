<?php

namespace App\Domain\License;

use App\Domain\License\Services\LicenseVerificationService;

class LicenseManager
{
    protected static ?LicenseVerificationService $instance = null;

    /**
     * Resolve the underlying service.
     */
    protected static function service(): LicenseVerificationService
    {
        if (static::$instance === null) {
            static::$instance = app(LicenseVerificationService::class);
        }

        return static::$instance;
    }

    /**
     * Check if a feature is enabled.
     *
     * Usage: LicenseManager::has('crm')
     */
    public static function has(string $feature): bool
    {
        return static::service()->hasFeature($feature);
    }

    /**
     * Check if the license is active.
     *
     * Usage: LicenseManager::active()
     */
    public static function active(): bool
    {
        return static::service()->isActive();
    }

    /**
     * Get the current plan name.
     *
     * Usage: LicenseManager::plan()
     */
    public static function plan(): ?string
    {
        return static::service()->plan();
    }

    /**
     * Refresh the license cache from HQ.
     *
     * Usage: LicenseManager::refresh()
     */
    public static function refresh(): bool
    {
        return static::service()->refresh();
    }
}
