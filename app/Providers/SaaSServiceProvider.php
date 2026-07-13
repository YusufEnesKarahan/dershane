<?php

declare(strict_types=1);

namespace App\Providers;

use App\Core\Services\EditionManager;
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
        // Bind Edition Manager
        $this->app->singleton(EditionManager::class, function (): EditionManager {
            return new EditionManager;
        });

        // Bind Feature Flag Service
        $this->app->singleton('saas.feature', function (): FeatureFlagService {
            return $this->app->make(FeatureFlagService::class);
        });

        // Bind Theme Service for Whitelabeling
        $this->app->singleton('saas.theme', function (): ThemeService {
            return new ThemeService;
        });

        // Load helpers dynamically if helper files exist
        $helperPath = app_path('Core/Helpers/Helpers.php');
        if (file_exists($helperPath)) {
            require_once $helperPath;
        }

        $saasHelperPath = app_path('Core/Helpers/SaaS.php');
        if (file_exists($saasHelperPath)) {
            require_once $saasHelperPath;
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

        // Register Blade directive for Edition checks
        Blade::if('edition', function (string $edition): bool {
            return $this->app->make(EditionManager::class)->current() === $edition;
        });

        // Register Blade directive to render theme custom styling variables
        Blade::directive('themeStyles', function (): string {
            return "<?php echo '<style>' . app('saas.theme')->renderCssVariables() . '</style>'; ?>";
        });

        // Register Blade directive for Dynamic SEO Tags
        Blade::directive('seo', function (string $expression): string {
            $expression = $expression ?: '[]';

            return "<?php echo app('saas.theme')->renderSeoTags({$expression}); ?>";
        });

        // Register Blade directive for Breadcrumbs rendering
        Blade::directive('breadcrumbs', function (string $expression): string {
            return "<?php echo view('components.breadcrumb', ['items' => {$expression}])->render(); ?>";
        });
    }
}
