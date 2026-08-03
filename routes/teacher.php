<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Teacher\TeacherDashboardController;
use App\Http\Controllers\Teacher\TeacherClassController;
use App\Http\Controllers\Teacher\TeacherAttendanceController;
use App\Http\Controllers\Teacher\TeacherHomeworkController;

Route::middleware(['auth', 'role:Teacher|Super Admin'])->prefix('teacher')->name('teacher.')->group(function () {
    Route::get('dashboard', [TeacherDashboardController::class, 'index'])->name('dashboard');
    Route::get('classes', [TeacherClassController::class, 'index'])->name('classes');
    
    // Attendance routes
    Route::get('attendance', [TeacherAttendanceController::class, 'index'])
        ->middleware('permission:attendance.view')
        ->name('attendance');
    Route::post('attendance', [TeacherAttendanceController::class, 'store'])
        ->middleware('permission:attendance.update')
        ->name('attendance.store');

    // Homework routes
    Route::get('homework', [TeacherHomeworkController::class, 'index'])
        ->middleware('permission:homeworks.view')
        ->name('homework');
    Route::post('homework', [TeacherHomeworkController::class, 'store'])
        ->middleware('permission:homeworks.manage')
        ->name('homework.store');
    Route::post('homework/evaluate', [TeacherHomeworkController::class, 'evaluate'])
        ->middleware('permission:homeworks.manage')
        ->name('homework.evaluate');

    // Analytics routes
    Route::get('analytics', [TeacherDashboardController::class, 'analytics'])->name('analytics');
});
