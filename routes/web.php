<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Web Routes Registry
|--------------------------------------------------------------------------
|
| This file is the primary entry point for all web-facing routes. It includes
| sub-route files for Frontend website, Admin dashboard, and Auth forms.
|
*/

require __DIR__.'/frontend.php';
require __DIR__.'/admin.php';
require __DIR__.'/parent.php';
require __DIR__.'/student.php';
require __DIR__.'/teacher.php';
require __DIR__.'/auth.php';

Route::get('/health', [\App\Http\Controllers\HealthController::class, 'check']);

Route::middleware(['auth'])->group(function () {
    Route::get('/health/queue', [\App\Http\Controllers\HealthController::class, 'queue']);
    Route::middleware(['role:Super Admin'])->get('/health/details', [\App\Http\Controllers\HealthController::class, 'details']);

    Route::middleware(['role:tenant_admin'])->get('/dashboard', [\App\Http\Controllers\Dashboard\TenantDashboardController::class, 'index'])->name('tenant.dashboard');
    Route::middleware(['role:teacher'])->get('/teacher/dashboard', [\App\Http\Controllers\Dashboard\TeacherDashboardController::class, 'index'])->name('teacher.dashboard');
    Route::middleware(['role:staff'])->get('/staff/dashboard', [\App\Http\Controllers\Dashboard\StaffDashboardController::class, 'index'])->name('staff.dashboard');
});

// Installation Wizard Routes
Route::prefix('install')->name('install.')->group(function () {
    Route::get('/', [\App\Http\Controllers\InstallController::class, 'welcome'])->name('welcome');
    Route::get('/requirements', [\App\Http\Controllers\InstallController::class, 'requirements'])->name('requirements');
    Route::get('/database', [\App\Http\Controllers\InstallController::class, 'database'])->name('database');

    Route::post('/database/migrate', [\App\Http\Controllers\InstallController::class, 'runMigration'])->name('migrate');
    Route::get('/admin', [\App\Http\Controllers\InstallController::class, 'admin'])->name('admin');
    Route::post('/admin', [\App\Http\Controllers\InstallController::class, 'storeAdmin'])->name('storeAdmin');
    Route::get('/finish', [\App\Http\Controllers\InstallController::class, 'finish'])->name('finish');
});
