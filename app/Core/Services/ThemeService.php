<?php

declare(strict_types=1);

namespace App\Core\Services;

use Illuminate\Support\Facades\Config;

class ThemeService
{
    /**
     * Get the primary color code.
     */
    public function getPrimaryColor(): string
    {
        return Config::get('brand.colors.primary', '#4f46e5');
    }

    /**
     * Get the secondary color code.
     */
    public function getSecondaryColor(): string
    {
        return Config::get('brand.colors.secondary', '#0891b2');
    }

    /**
     * Get the logo path for light/dark theme.
     */
    public function getLogoPath(string $mode = 'light'): string
    {
        return Config::get("brand.logo.{$mode}", '/assets/branding/logo-light.svg');
    }

    /**
     * Get the favicon path.
     */
    public function getFaviconPath(): string
    {
        return Config::get('brand.logo.favicon', '/favicon.ico');
    }

    /**
     * Get the active layout theme name.
     */
    public function getThemeName(): string
    {
        return Config::get('brand.layout.theme', 'default');
    }

    /**
     * Generate HTML safe inline CSS variables for branding.
     */
    public function renderCssVariables(): string
    {
        $primary = $this->getPrimaryColor();
        $secondary = $this->getSecondaryColor();
        $font = Config::get('brand.typography.font_family', 'sans-serif');

        return "
            :root {
                --brand-primary: {$primary};
                --brand-secondary: {$secondary};
                --brand-font: {$font};
            }
        ";
    }
}
