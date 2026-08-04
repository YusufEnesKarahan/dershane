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
    
    // Guidance
    Route::get('child-performance', [\App\Http\Controllers\Parent\ParentGuidanceController::class, 'childPerformance'])->name('guidance.performance');
    Route::get('child-guidance', [\App\Http\Controllers\Parent\ParentGuidanceController::class, 'childGuidance'])->name('guidance.dashboard');
    
    // Attendance
    Route::get('child-attendance', [\App\Http\Controllers\Parent\ParentAttendanceController::class, 'index'])->name('attendance.index');
    
    // Exams
    Route::get('students/{student}/exams', [\App\Http\Controllers\Parent\ParentExamController::class, 'index'])->name('exams.index');
    Route::get('students/{student}/exams/{exam}', [\App\Http\Controllers\Parent\ParentExamController::class, 'showResult'])->name('exams.show');

    // Homeworks
    Route::get('homeworks', [\App\Http\Controllers\Parent\ParentHomeworkController::class, 'index'])->name('homeworks.index');
    Route::get('homeworks/{homework}', [\App\Http\Controllers\Parent\ParentHomeworkController::class, 'show'])->name('homeworks.show');

    // Schedule
    Route::get('schedule', [\App\Http\Controllers\Parent\ParentScheduleController::class, 'index'])->name('schedule.index');
});
