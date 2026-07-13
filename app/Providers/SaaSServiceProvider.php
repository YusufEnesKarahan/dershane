<?php

declare(strict_types=1);

namespace App\Providers;

use App\Core\Services\FeatureFlagService;
use App\Core\Services\ThemeService;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class SaaSServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Bind Feature Flag Service
        $this->app->singleton('saas.feature', function (): FeatureFlagService {
            return new FeatureFlagService;
        });

        // Bind Theme Service for Whitelabeling
        $this->app->singleton('saas.theme', function (): ThemeService {
            return new ThemeService;
        });

        // Load helpers dynamically if helper file exists
        $helperPath = app_path('Core/Helpers/Helpers.php');
        if (file_exists($helperPath)) {
            require_once $helperPath;
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register Blade directive for Feature Flags
        Blade::if('feature', function (string $feature): bool {
            return $this->app->make('saas.feature')->isActive($feature);
        });

        // Register Blade directive to render theme custom styling variables
        Blade::directive('themeStyles', function (): string {
            return "<?php echo '<style>' . app('saas.theme')->renderCssVariables() . '</style>'; ?>";
        });
    }
}
