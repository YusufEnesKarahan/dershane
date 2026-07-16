<?php
namespace App\Domain\Platform\Services;

use Illuminate\Support\Facades\Storage;

class ThemeService
{
    public function __construct(protected PlatformSettingsService $settingsService) {}

    public function compileThemeCss(): void
    {
        $primary = $this->settingsService->get('theme.primary_color', '#4f46e5');
        $secondary = $this->settingsService->get('theme.secondary_color', '#06b6d4');
        $accent = $this->settingsService->get('theme.accent_color', '#f59e0b');
        $background = $this->settingsService->get('theme.background_color', '#f8fafc');
        $sidebar = $this->settingsService->get('theme.sidebar_color', '#0f172a');
        $radius = $this->settingsService->get('theme.border_radius', '12px');
        $spacing = $this->settingsService->get('theme.spacing', '16px');
        $font = $this->settingsService->get('theme.typography', 'Instrument Sans, sans-serif');

        $css = "
        :root {
            --color-primary: {$primary};
            --color-secondary: {$secondary};
            --color-accent: {$accent};
            --color-background: {$background};
            --color-sidebar: {$sidebar};
            --border-radius: {$radius};
            --layout-spacing: {$spacing};
            --font-family: '{$font}';
        }
        ";

        // Save css to public/css/theme_custom.css
        $publicCssDir = public_path('css');
        if (!is_dir($publicCssDir)) {
            mkdir($publicCssDir, 0755, true);
        }
        file_put_contents($publicCssDir . '/theme_custom.css', $css);
    }
}
