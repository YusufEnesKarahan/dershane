<?php

declare(strict_types=1);

namespace App\Core\Services;

use Illuminate\Support\Facades\Config;

class BrandManager
{
    /**
     * Get the brand name.
     */
    public function getName(): string
    {
        return Config::get('brand.name', 'Eğitim Kurumu SaaS');
    }

    /**
     * Get logo light or dark path.
     */
    public function getLogo(string $type = 'light'): string
    {
        return Config::get("brand.logo.{$type}", '');
    }

    /**
     * Get custom brand configurations.
     */
    public function getSetting(string $key, mixed $default = null): mixed
    {
        return Config::get("brand.{$key}", $default);
    }
}
