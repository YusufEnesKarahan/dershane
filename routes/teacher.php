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
    Route::get('homeworks', [TeacherHomeworkController::class, 'index'])
        ->middleware('permission:homework.view')
        ->name('homeworks.index');
    Route::get('homeworks/{homework}', [TeacherHomeworkController::class, 'show'])
        ->middleware('permission:homework.view')
        ->name('homeworks.show');
    Route::post('homeworks/{homework}/submissions/{submission}/grade', [TeacherHomeworkController::class, 'grade'])
        ->middleware('permission:homework.grade')
        ->name('homeworks.submissions.grade');

    // Analytics routes
    Route::get('analytics', [TeacherDashboardController::class, 'analytics'])->name('analytics');
});
