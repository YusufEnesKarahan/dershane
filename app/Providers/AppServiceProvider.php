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
        $this->app->bind(
            \App\Domain\CMS\Services\MarkdownRendererInterface::class,
            \App\Domain\CMS\Services\CommonMarkRenderer::class
        );
        $this->app->bind(
            \App\Domain\Media\Adapters\StorageAdapterInterface::class,
            \App\Domain\Media\Adapters\LocalStorageAdapter::class
        );
        $this->app->bind(
            \App\Domain\Media\Cache\MediaCacheInvalidatorInterface::class,
            \App\Domain\Media\Cache\MediaCacheInvalidator::class
        );

        $this->app->singleton(\App\Domain\Media\Conversions\ConversionStrategyRegistry::class, function () {
            $registry = new \App\Domain\Media\Conversions\ConversionStrategyRegistry();
            $registry->register(new \App\Domain\Media\Conversions\ThumbnailConversionStrategy());
            $registry->register(new \App\Domain\Media\Conversions\WebpConversionStrategy());
            return $registry;
        });
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

        \App\Models\Page::observe(\App\Observers\PageObserver::class);
        \App\Models\Media::observe(\App\Observers\MediaObserver::class);
        \App\Models\PlatformSetting::observe(\App\Observers\PlatformSettingObserver::class);
        \App\Models\Blog::observe(\App\Observers\BlogObserver::class);
        \App\Models\BlogComment::observe(\App\Observers\BlogCommentObserver::class);
        \App\Models\BlogCategory::observe(\App\Observers\BlogCategoryObserver::class);
        \App\Models\BlogTag::observe(\App\Observers\BlogTagObserver::class);
        \App\Models\Teacher::observe(\App\Observers\TeacherObserver::class);
        \App\Models\TeacherDocument::observe(\App\Observers\TeacherDocumentObserver::class);
        \App\Models\TeacherSchedule::observe(\App\Observers\TeacherScheduleObserver::class);
        \App\Models\TeacherPerformance::observe(\App\Observers\TeacherPerformanceObserver::class);
        \App\Models\Course::observe(\App\Observers\CourseObserver::class);
        \App\Models\Classroom::observe(\App\Observers\ClassroomObserver::class);
        \App\Models\Student::observe(\App\Observers\StudentObserver::class);
        \App\Models\Attendance::observe(\App\Observers\AttendanceObserver::class);
        \App\Models\Exam::observe(\App\Observers\ExamObserver::class);
        \App\Models\Assignment::observe(\App\Observers\AssignmentObserver::class);
        \App\Models\Invoice::observe(\App\Observers\InvoiceObserver::class);
    }
}
