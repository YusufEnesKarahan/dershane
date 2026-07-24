<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Parent\ParentDashboardController;
use App\Http\Controllers\Parent\ParentNotificationController;

Route::middleware(['auth', 'role:Parent|Administrator'])->prefix('parent')->name('parent.')->group(function () {
    Route::get('dashboard', [ParentDashboardController::class, 'index'])->name('dashboard');
    Route::get('notifications', [ParentNotificationController::class, 'index'])->name('notifications');
});
