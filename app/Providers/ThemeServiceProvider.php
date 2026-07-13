<?php

declare(strict_types=1);

namespace App\Providers;

use App\Core\Services\BrandManager;
use App\Core\Services\ThemeManager;
use Illuminate\Support\ServiceProvider;

class ThemeServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton('saas.theme_manager', function (): ThemeManager {
            return new ThemeManager;
        });

        $this->app->singleton('saas.brand_manager', function (): BrandManager {
            return new BrandManager;
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
