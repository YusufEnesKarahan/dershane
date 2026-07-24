<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Teacher\TeacherDashboardController;
use App\Http\Controllers\Teacher\TeacherClassController;
use App\Http\Controllers\Teacher\TeacherAttendanceController;
use App\Http\Controllers\Teacher\TeacherHomeworkController;

Route::middleware(['auth', 'role:Teacher|Administrator'])->prefix('teacher')->name('teacher.')->group(function () {
    Route::get('dashboard', [TeacherDashboardController::class, 'index'])->name('dashboard');
    Route::get('classes', [TeacherClassController::class, 'index'])->name('classes');
    
    // Attendance routes
    Route::get('attendance', [TeacherAttendanceController::class, 'index'])->name('attendance');
    Route::post('attendance', [TeacherAttendanceController::class, 'store'])->name('attendance.store');

    // Homework routes
    Route::get('homework', [TeacherHomeworkController::class, 'index'])->name('homework');
    Route::post('homework', [TeacherHomeworkController::class, 'store'])->name('homework.store');
    Route::post('homework/evaluate', [TeacherHomeworkController::class, 'evaluate'])->name('homework.evaluate');

    // Analytics routes
    Route::get('analytics', [TeacherDashboardController::class, 'analytics'])->name('analytics');
});
