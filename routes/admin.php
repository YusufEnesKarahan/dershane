<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\UserProfileController;
use App\Http\Controllers\Admin\UserPreferenceController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\MediaController;
use App\Http\Controllers\Admin\MediaFolderController;

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
    Route::get('blogs', fn() => 'Blogs Placeholder')->name('blogs.index');
    Route::get('announcements', fn() => 'Announcements Placeholder')->name('announcements.index');

    // Education
    Route::get('students', fn() => 'Students Placeholder')->name('students.index');
    Route::get('teachers', fn() => 'Teachers Placeholder')->name('teachers.index');
    Route::get('courses', fn() => 'Courses Placeholder')->name('courses.index');
    Route::get('classrooms', fn() => 'Classrooms Placeholder')->name('classrooms.index');

    // Media Library
    Route::get('media-picker', [MediaController::class, 'pickerList'])->name('media.picker-list');
    Route::post('media-folders', [MediaFolderController::class, 'store'])->name('media-folders.store');
    Route::delete('media-folders/{folder}', [MediaFolderController::class, 'destroy'])->name('media-folders.destroy');
    Route::resource('media', MediaController::class);

    // CRM
    Route::get('leads', fn() => 'Leads Placeholder')->name('leads.index');
    Route::get('contacts', fn() => 'Contacts Placeholder')->name('contacts.index');

    // System
    Route::get('branches', fn() => 'Branches Placeholder')->name('branches.index');
    Route::get('settings', fn() => 'Settings Placeholder')->name('settings.index');
    Route::get('logs', fn() => 'Logs Placeholder')->name('logs.index');
});
