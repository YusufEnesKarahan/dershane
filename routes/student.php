<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Portal\StudentPortalController;
use App\Http\Controllers\Student\StudentHomeworkController;
use App\Http\Controllers\Student\FinancePortalController as StudentFinanceController;

Route::middleware(['auth', 'role:Student|Super Admin', \App\Http\Middleware\EnsureActiveBranch::class])
    ->prefix('student')
    ->name('student.')
    ->group(function () {
        Route::get('/dashboard', [StudentPortalController::class, 'dashboard'])->name('dashboard');
        Route::get('/courses', [StudentPortalController::class, 'courses'])->name('courses');
        
        // Notifications
        Route::middleware(['feature.access:notification'])->group(function () {
            Route::get('notifications', [\App\Http\Controllers\Student\StudentNotificationController::class, 'index'])->name('notifications.index');
            Route::post('notifications/{notification}/read', [\App\Http\Controllers\Student\StudentNotificationController::class, 'markAsRead'])->name('notifications.read');
            Route::post('notifications/read-all', [\App\Http\Controllers\Student\StudentNotificationController::class, 'markAllRead'])->name('notifications.read-all');
        });

        // Homeworks
        Route::middleware(['permission:homework.view', 'feature.access:homework'])->group(function() {
            Route::get('homework', [StudentHomeworkController::class, 'index'])->name('homework');
            Route::get('homeworks', [StudentHomeworkController::class, 'index'])->name('homeworks.index');
            Route::get('homeworks/{homework}', [StudentHomeworkController::class, 'show'])->name('homeworks.show');
            Route::post('homeworks/{homework}/submit', [StudentHomeworkController::class, 'submit'])->name('homeworks.submit');
        });

        // Finance
        Route::middleware(['feature.access:finance'])->group(function () {
            Route::get('my-payments', [StudentFinanceController::class, 'index'])->name('finance.index');
        });
        
        // Guidance & Performance
        Route::middleware(['feature.access:guidance'])->group(function () {
            Route::get('my-performance', [\App\Http\Controllers\Student\StudentPerformanceController::class, 'myPerformance'])->name('performance.dashboard');
            Route::get('my-goals', [\App\Http\Controllers\Student\StudentPerformanceController::class, 'myGoals'])->name('performance.goals');
        });
        
        // Attendance
        Route::middleware(['feature.access:attendance'])->group(function () {
            Route::get('attendance', [\App\Http\Controllers\Student\StudentAttendanceController::class, 'index'])->name('attendance');
            Route::get('my-attendance', [\App\Http\Controllers\Student\StudentAttendanceController::class, 'index'])->name('attendance.index');
        });
        
        // Exams
        Route::middleware(['feature.access:exam'])->group(function () {
            Route::get('exams', [\App\Http\Controllers\Student\StudentExamController::class, 'index'])->name('exams');
            Route::get('my-exams', [\App\Http\Controllers\Student\StudentExamController::class, 'index'])->name('exams.index');
            Route::get('my-exams/{exam}', [\App\Http\Controllers\Student\StudentExamController::class, 'showResult'])->name('exams.show');
        });

        // Schedule
        Route::middleware(['feature.access:schedule'])->group(function () {
            Route::get('my-schedule', [\App\Http\Controllers\Student\StudentScheduleController::class, 'index'])->name('schedule.index');
        });
    });
