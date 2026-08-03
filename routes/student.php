<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Portal\StudentPortalController;

Route::middleware(['auth', 'role:Student', 'permission:student.view_profile', \App\Http\Middleware\EnsureActiveBranch::class])
    ->prefix('student')
    ->name('student.')
    ->group(function () {
        Route::get('/dashboard', [StudentPortalController::class, 'dashboard'])->name('dashboard');
        
        // Notifications
        Route::get('notifications', [\App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
        Route::post('notifications/{id?}/read', [\App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.read');
    });
