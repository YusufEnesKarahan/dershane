<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            \App\Core\Contracts\ActivityLoggerInterface::class,
            \App\Core\Services\Logging\NullActivityLogger::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Implicitly grant "Administrator" role all permissions
        Gate::before(function ($user, $ability) {
            return $user->hasRole('Administrator') ? true : null;
        });

        Blade::directive('role', function ($role) {
            return "<?php if(auth()->check() && app(\App\Domain\Auth\Services\AuthorizationService::class)->hasRole(auth()->user(), {$role})): ?>";
        });

        Blade::directive('endrole', function () {
            return "<?php endif; ?>";
        });

        Blade::directive('permission', function ($permission) {
            return "<?php if(auth()->check() && app(\App\Domain\Auth\Services\AuthorizationService::class)->hasPermission(auth()->user(), {$permission})): ?>";
        });

        Blade::directive('endpermission', function () {
            return "<?php endif; ?>";
        });

        Blade::directive('cananypermission', function ($permissions) {
            return "<?php 
                \$hasAny = false;
                \$perms = is_array({$permissions}) ? {$permissions} : explode(',', str_replace(['\'', '\"', ' '], '', {$permissions}));
                foreach (\$perms as \$p) {
                    if (app(\App\Domain\Auth\Services\AuthorizationService::class)->hasPermission(auth()->user(), trim(\$p))) {
                        \$hasAny = true;
                        break;
                    }
                }
                if(\$hasAny):
            ?>";
        });

        Blade::directive('endcananypermission', function () {
            return "<?php endif; ?>";
        });
    }
}
