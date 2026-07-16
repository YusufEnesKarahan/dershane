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
        $this->app->bind(
            \App\Core\Repositories\Interfaces\PageRepositoryInterface::class,
            \App\Core\Repositories\PageRepository::class
        );
        $this->app->bind(
            \App\Core\Repositories\Interfaces\MediaRepositoryInterface::class,
            \App\Core\Repositories\MediaRepository::class
        );
        $this->app->bind(
            \App\Core\Repositories\Interfaces\MediaFolderRepositoryInterface::class,
            \App\Core\Repositories\MediaFolderRepository::class
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
