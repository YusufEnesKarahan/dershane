<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Portal\ParentPortalController;

Route::middleware(['auth', 'role:Parent|Super Admin', \App\Http\Middleware\EnsureActiveBranch::class])->prefix('parent')->name('parent.')->group(function () {
    Route::get('dashboard', [ParentPortalController::class, 'dashboard'])
        ->middleware('permission:parent.view_child')
        ->name('dashboard');

    // Notifications
    Route::get('notifications', [\App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::post('notifications/{id?}/read', [\App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.read');

    // Finance
    Route::get('child-payments', [\App\Http\Controllers\Parent\FinancePortalController::class, 'index'])->name('finance.index');
});
