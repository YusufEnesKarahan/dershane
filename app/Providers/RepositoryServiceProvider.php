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
        $this->app->bind(
            \App\Core\Repositories\Interfaces\PlatformSettingRepositoryInterface::class,
            \App\Core\Repositories\PlatformSettingRepository::class
        );
        $this->app->bind(
            \App\Core\Repositories\Interfaces\SettingGroupRepositoryInterface::class,
            \App\Core\Repositories\SettingGroupRepository::class
        );
        $this->app->bind(
            \App\Core\Repositories\Interfaces\BlogRepositoryInterface::class,
            \App\Core\Repositories\BlogRepository::class
        );
        $this->app->bind(
            \App\Core\Repositories\Interfaces\BlogCategoryRepositoryInterface::class,
            \App\Core\Repositories\BlogCategoryRepository::class
        );
        $this->app->bind(
            \App\Core\Repositories\Interfaces\BlogTagRepositoryInterface::class,
            \App\Core\Repositories\BlogTagRepository::class
        );
        $this->app->bind(
            \App\Core\Repositories\Interfaces\CommentRepositoryInterface::class,
            \App\Core\Repositories\CommentRepository::class
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
