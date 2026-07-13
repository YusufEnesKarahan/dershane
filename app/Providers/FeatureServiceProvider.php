<?php

declare(strict_types=1);

namespace App\Providers;

use App\Core\Services\FeatureManager;
use Illuminate\Support\ServiceProvider;

class FeatureServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton('saas.feature_manager', function (): FeatureManager {
            return new FeatureManager;
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
