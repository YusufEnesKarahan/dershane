<?php

declare(strict_types=1);

namespace App\Providers;

use App\Core\Services\SettingsManager;
use Illuminate\Support\ServiceProvider;

class SettingsServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton('saas.settings_manager', function (): SettingsManager {
            return new SettingsManager;
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
