<?php

declare(strict_types=1);

namespace App\Core\Support\Facades;

use App\Core\Services\FeatureFlagService;

/**
 * @method static bool isActive(string $feature)
 * @method static string getVersionName()
 *
 * @see FeatureFlagService
 */
class Feature
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return 'saas.feature';
    }
}
