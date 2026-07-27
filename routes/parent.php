<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Parent\ParentDashboardController;
use App\Http\Controllers\Parent\ParentNotificationController;

Route::middleware(['auth', 'role:Parent|Super Admin'])->prefix('parent')->name('parent.')->group(function () {
    Route::get('dashboard', [ParentDashboardController::class, 'index'])
        ->middleware('permission:students.view')
        ->name('dashboard');
        
    Route::get('notifications', [ParentNotificationController::class, 'index'])
        ->middleware('permission:notifications.view')
        ->name('notifications');
});
