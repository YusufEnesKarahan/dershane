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

    /**
     * Get the Whitelabel Company display name.
     */
    public function getCompanyName(): string
    {
        return Config::get('brand.company.name', 'Limit VIP Eğitim Hizmetleri A.Ş.');
    }

    /**
     * Get the Whitelabel Company phone number.
     */
    public function getPhone(): string
    {
        return Config::get('brand.company.phone', '+90 216 555 12 34');
    }

    /**
     * Get the Whitelabel Company email address.
     */
    public function getMail(): string
    {
        return Config::get('brand.company.mail', 'info@limitvip.com');
    }

    /**
     * Get the Whitelabel Company physical address.
     */
    public function getAddress(): string
    {
        return Config::get('brand.company.address', 'Caddebostan Mah. Bağdat Caddesi No:245/4 Kadıköy/İstanbul');
    }

    /**
     * Get the Whitelabel Company social media URLs.
     */
    public function getSocials(): array
    {
        return Config::get('brand.company.socials', []);
    }
}
