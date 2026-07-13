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

    /**
     * Render SEO Meta Tags dynamically.
     */
    public function renderSeoTags(array $seo = []): string
    {
        $siteName = Config::get('brand.name', 'Eğitim Kurumu SaaS');
        $title = $seo['title'] ?? Config::get('brand.seo.title_default', 'Modern Eğitim Yönetim Sistemi');
        $titleFormatted = $title . ' | ' . $siteName;
        $description = $seo['description'] ?? Config::get('brand.seo.description_default', 'Dershane ve etüt merkezleri için SaaS yönetim altyapısı.');
        $keywords = $seo['keywords'] ?? Config::get('brand.seo.keywords_default', 'dershane, kurs, etüt, eğitim');
        $image = $seo['image'] ?? asset(Config::get('brand.logo.og_image', '/assets/branding/og-image.jpg'));
        $url = $seo['url'] ?? url()->current();
        $robots = $seo['robots'] ?? 'index, follow';

        return "
            <title>" . e($titleFormatted) . "</title>
            <meta name=\"description\" content=\"" . e($description) . "\">
            <meta name=\"keywords\" content=\"" . e($keywords) . "\">
            <meta name=\"robots\" content=\"" . e($robots) . "\">
            <link rel=\"canonical\" href=\"" . e($url) . "\">
            <meta property=\"og:type\" content=\"website\">
            <meta property=\"og:title\" content=\"" . e($title) . "\">
            <meta property=\"og:description\" content=\"" . e($description) . "\">
            <meta property=\"og:image\" content=\"" . e($image) . "\">
            <meta property=\"og:url\" content=\"" . e($url) . "\">
            <meta property=\"og:site_name\" content=\"" . e($siteName) . "\">
            <meta name=\"twitter:card\" content=\"summary_large_image\">
            <meta name=\"twitter:title\" content=\"" . e($title) . "\">
            <meta name=\"twitter:description\" content=\"" . e($description) . "\">
            <meta name=\"twitter:image\" content=\"" . e($image) . "\">
        ";
    }
}

