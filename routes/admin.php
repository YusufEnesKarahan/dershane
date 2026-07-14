<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\ProfileController;

// Admin Framework Routes
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    
    // Dashboard is handled in auth.php right now, but let's keep all placeholder routes here
    // Route::get('dashboard', [ProfileController::class, 'dashboard'])->name('dashboard');

    // Access Management
    Route::get('users', fn() => 'Users Placeholder')->name('users.index');
    Route::get('roles', fn() => 'Roles Placeholder')->name('roles.index');
    Route::get('permissions', fn() => 'Permissions Placeholder')->name('permissions.index');

    // CMS
    Route::get('pages', fn() => 'Pages Placeholder')->name('pages.index');
    Route::get('blogs', fn() => 'Blogs Placeholder')->name('blogs.index');
    Route::get('announcements', fn() => 'Announcements Placeholder')->name('announcements.index');

    // Education
    Route::get('students', fn() => 'Students Placeholder')->name('students.index');
    Route::get('teachers', fn() => 'Teachers Placeholder')->name('teachers.index');
    Route::get('courses', fn() => 'Courses Placeholder')->name('courses.index');
    Route::get('classrooms', fn() => 'Classrooms Placeholder')->name('classrooms.index');

    // CRM
    Route::get('leads', fn() => 'Leads Placeholder')->name('leads.index');
    Route::get('contacts', fn() => 'Contacts Placeholder')->name('contacts.index');

    // System
    Route::get('branches', fn() => 'Branches Placeholder')->name('branches.index');
    Route::get('settings', fn() => 'Settings Placeholder')->name('settings.index');
    Route::get('logs', fn() => 'Logs Placeholder')->name('logs.index');
});
