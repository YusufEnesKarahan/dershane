<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            \App\Core\Repositories\Interfaces\UserRepositoryInterface::class,
            \App\Core\Repositories\UserRepository::class
        );
        $this->app->bind(
            \App\Core\Repositories\Interfaces\RoleRepositoryInterface::class,
            \App\Core\Repositories\RoleRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
