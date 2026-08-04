<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Portal\StudentPortalController;
use App\Http\Controllers\Student\StudentHomeworkController;
use App\Http\Controllers\Student\FinancePortalController as StudentFinanceController;

Route::middleware(['auth', 'role:Student', 'permission:student.view_profile', \App\Http\Middleware\EnsureActiveBranch::class])
    ->prefix('student')
    ->name('student.')
    ->group(function () {
        Route::get('/dashboard', [StudentPortalController::class, 'dashboard'])->name('dashboard');
        
        // Notifications
        Route::get('notifications', [\App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
        Route::post('notifications/{id?}/read', [\App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.read');

        // Homeworks
        Route::middleware('permission:homework.view')->group(function() {
            Route::get('homeworks', [StudentHomeworkController::class, 'index'])->name('homeworks.index');
            Route::get('homeworks/{homework}', [StudentHomeworkController::class, 'show'])->name('homeworks.show');
            Route::post('homeworks/{homework}/submit', [StudentHomeworkController::class, 'submit'])->name('homeworks.submit');
        });

        // Finance
        Route::get('my-payments', [StudentFinanceController::class, 'index'])->name('finance.index');
        
        // Guidance & Performance
        Route::get('my-performance', [\App\Http\Controllers\Student\StudentPerformanceController::class, 'myPerformance'])->name('performance.dashboard');
        Route::get('my-goals', [\App\Http\Controllers\Student\StudentPerformanceController::class, 'myGoals'])->name('performance.goals');
        
        // Attendance
        Route::get('my-attendance', [\App\Http\Controllers\Student\StudentAttendanceController::class, 'index'])->name('attendance.index');
        
        // Exams
        Route::get('my-exams', [\App\Http\Controllers\Student\StudentExamController::class, 'index'])->name('exams.index');
        Route::get('my-exams/{exam}', [\App\Http\Controllers\Student\StudentExamController::class, 'showResult'])->name('exams.show');

        // Schedule
        Route::get('my-schedule', [\App\Http\Controllers\Student\StudentScheduleController::class, 'index'])->name('schedule.index');
    });
