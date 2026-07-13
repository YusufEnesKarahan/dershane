<?php

declare(strict_types=1);

namespace App\Core\Services;

use Illuminate\Support\Facades\Config;

class SettingsManager
{
    /**
     * Get a setting by key with fallback default.
     */
    public function get(string $key, mixed $default = null): mixed
    {
        return Config::get("settings.{$key}", $default);
    }

    /**
     * Get default currency.
     */
    public function getCurrency(): string
    {
        return Config::get('settings.currency.default', 'TRY');
    }

    /**
     * Get default pagination limit.
     */
    public function getPaginationLimit(): int
    {
        return (int) Config::get('settings.pagination.default_limit', 15);
    }
}
