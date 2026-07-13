<?php

declare(strict_types=1);

namespace App\Core\Services;

use Illuminate\Support\Facades\Config;

class ThemeManager
{
    /**
     * Get the active theme identifier.
     */
    public function getActiveTheme(): string
    {
        return Config::get('theme.active', 'default');
    }

    /**
     * Get stylesheet paths for the active theme.
     *
     * @return array<int, string>
     */
    public function getStylesheets(): array
    {
        $active = $this->getActiveTheme();

        return Config::get("theme.themes.{$active}.stylesheets", []);
    }
}
