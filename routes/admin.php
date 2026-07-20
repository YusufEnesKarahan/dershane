<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\UserProfileController;
use App\Http\Controllers\Admin\UserPreferenceController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\MediaController;
use App\Http\Controllers\Admin\MediaFolderController;
use App\Http\Controllers\Admin\PlatformSettingController;
use App\Http\Controllers\Admin\BlogController;
use App\Http\Controllers\Admin\BlogCategoryController;
use App\Http\Controllers\Admin\BlogTagController;
use App\Http\Controllers\Admin\BlogCommentController;
use App\Http\Controllers\Admin\TeacherController;
use App\Http\Controllers\Admin\TeacherScheduleController;
use App\Http\Controllers\Admin\TeacherPerformanceController;
use App\Http\Controllers\Admin\TeacherSalaryController;
use App\Http\Controllers\Admin\TeacherContractController;
use App\Http\Controllers\Admin\CourseController;
use App\Http\Controllers\Admin\CourseLevelController;

// Admin Framework Routes
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    
    // Dashboard is handled in auth.php right now, but let's keep all placeholder routes here
    // Route::get('dashboard', [ProfileController::class, 'dashboard'])->name('dashboard');

    // Access Management
    Route::post('users/bulk', [UserController::class, 'bulk'])->name('users.bulk');
    Route::post('users/{id}/restore', [UserController::class, 'restore'])->name('users.restore');
    Route::resource('users', UserController::class);
    
    Route::get('profile', [UserProfileController::class, 'edit'])->name('profile.edit');
    Route::post('profile', [UserProfileController::class, 'update'])->name('profile.update');
    Route::post('profile/password', [UserProfileController::class, 'password'])->name('profile.password');
    Route::post('profile/avatar', [UserProfileController::class, 'avatar'])->name('profile.avatar');
    Route::post('preferences', [UserPreferenceController::class, 'update'])->name('preferences.update');

    Route::post('roles/bulk', [RoleController::class, 'bulk'])->name('roles.bulk');
    Route::post('roles/{id}/restore', [RoleController::class, 'restore'])->name('roles.restore');
    Route::get('roles/{role}/clone', [RoleController::class, 'showClone'])->name('roles.showClone');
    Route::post('roles/{role}/clone', [RoleController::class, 'clone'])->name('roles.clone');
    Route::resource('roles', RoleController::class);
    Route::get('permissions', fn() => 'Permissions Placeholder')->name('permissions.index');
    
    // CMS
    Route::post('pages/bulk', [PageController::class, 'bulk'])->name('pages.bulk');
    Route::post('pages/{id}/restore', [PageController::class, 'restore'])->name('pages.restore');
    Route::post('pages/{page}/publish', [PageController::class, 'publish'])->name('pages.publish');
    Route::post('pages/{page}/duplicate', [PageController::class, 'duplicate'])->name('pages.duplicate');
    Route::get('pages/{page}/preview', [PageController::class, 'preview'])->name('pages.preview');
    Route::resource('pages', PageController::class);
    // Blog Suite
    Route::get('blogs/analytics', [BlogController::class, 'analytics'])->name('blogs.analytics');
    Route::post('blogs/{blog}/publish', [BlogController::class, 'publish'])->name('blogs.publish');
    Route::post('blogs/{blog}/archive', [BlogController::class, 'archive'])->name('blogs.archive');
    Route::post('blogs/{blog}/duplicate', [BlogController::class, 'duplicate'])->name('blogs.duplicate');
    Route::post('blogs/{blog}/revisions/{revision}/restore', [BlogController::class, 'restoreRevision'])->name('blogs.revisions.restore');
    Route::resource('blogs', BlogController::class);

    // Blog Categories & Tags
    Route::resource('categories', BlogCategoryController::class)->only(['index', 'store', 'destroy']);
    Route::post('tags/merge', [BlogTagController::class, 'merge'])->name('tags.merge');
    Route::resource('tags', BlogTagController::class)->only(['index', 'store', 'destroy']);

    // Comments Moderation
    Route::post('comments/{comment}/approve', [BlogCommentController::class, 'approve'])->name('comments.approve');
    Route::post('comments/{comment}/reject', [BlogCommentController::class, 'reject'])->name('comments.reject');
    Route::post('comments/{comment}/spam', [BlogCommentController::class, 'spam'])->name('comments.spam');
    Route::resource('comments', BlogCommentController::class)->only(['index', 'destroy']);

    Route::get('announcements', fn() => 'Announcements Placeholder')->name('announcements.index');

    // Education
    Route::get('students', fn() => 'Students Placeholder')->name('students.index');
    Route::get('teachers/analytics', [TeacherController::class, 'analytics'])->name('teachers.analytics');
    Route::resource('teachers', TeacherController::class);
    Route::resource('teachers-schedules', TeacherScheduleController::class)->only(['index', 'store'])->names([
        'index' => 'teachers.schedules.index',
        'store' => 'teachers.schedules.store',
    ]);
    Route::resource('teachers-performance', TeacherPerformanceController::class)->only(['index', 'store'])->names([
        'index' => 'teachers.performance.index',
        'store' => 'teachers.performance.store',
    ]);
    Route::resource('teachers-salary', TeacherSalaryController::class)->only(['index', 'store'])->names([
        'index' => 'teachers.salary.index',
        'store' => 'teachers.salary.store',
    ]);
    Route::resource('teachers-contracts', TeacherContractController::class)->only(['index', 'store'])->names([
        'index' => 'teachers.contracts.index',
        'store' => 'teachers.contracts.store',
    ]);
    Route::get('courses/analytics', [CourseController::class, 'analytics'])->name('courses.analytics');
    Route::resource('courses-levels', CourseLevelController::class)->only(['index', 'store', 'destroy'])->names([
        'index' => 'courses.levels.index',
        'store' => 'courses.levels.store',
        'destroy' => 'courses.levels.destroy',
    ]);
    Route::resource('courses', CourseController::class);
    Route::get('classrooms', fn() => 'Classrooms Placeholder')->name('classrooms.index');

    // Media Library
    Route::get('media-picker', [MediaController::class, 'pickerList'])->name('media.picker-list');
    Route::get('media/{media}/download', [MediaController::class, 'download'])->name('media.download');
    Route::post('media-folders', [MediaFolderController::class, 'store'])->name('media-folders.store');
    Route::delete('media-folders/{folder}', [MediaFolderController::class, 'destroy'])->name('media-folders.destroy');
    Route::resource('media', MediaController::class);

    // CRM
    Route::get('leads', fn() => 'Leads Placeholder')->name('leads.index');
    Route::get('contacts', fn() => 'Contacts Placeholder')->name('contacts.index');

    // System
    Route::get('branches', fn() => 'Branches Placeholder')->name('branches.index');
    Route::get('settings', [PlatformSettingController::class, 'index'])->name('settings.index');
    Route::post('settings', [PlatformSettingController::class, 'update'])->name('settings.update');
    Route::post('settings/test-mail', [PlatformSettingController::class, 'testMail'])->name('settings.test-mail');
    Route::post('settings/test-storage', [PlatformSettingController::class, 'testStorage'])->name('settings.test-storage');
    Route::get('settings/export', [PlatformSettingController::class, 'export'])->name('settings.export');
    Route::post('settings/import', [PlatformSettingController::class, 'import'])->name('settings.import');
    Route::post('settings/reset', [PlatformSettingController::class, 'reset'])->name('settings.reset');
    Route::get('logs', fn() => 'Logs Placeholder')->name('logs.index');
});
