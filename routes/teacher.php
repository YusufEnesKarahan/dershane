<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Teacher\TeacherDashboardController;
use App\Http\Controllers\Teacher\TeacherClassController;
use App\Http\Controllers\Teacher\TeacherAttendanceController;
use App\Http\Controllers\Teacher\TeacherHomeworkController;

Route::middleware(['auth', 'role:Teacher|Super Admin', \App\Http\Middleware\EnsureActiveBranch::class])->prefix('teacher')->name('teacher.')->group(function () {
    Route::get('dashboard', [TeacherDashboardController::class, 'index'])->name('dashboard');
    Route::get('classes', [TeacherClassController::class, 'index'])->name('classes');
    
    // Attendance routes
    Route::middleware(['permission:attendance.view', 'feature.access:attendance'])->group(function () {
        Route::get('attendance', [TeacherAttendanceController::class, 'myClasses'])->name('attendance.index');
        Route::get('attendance/{session}', [TeacherAttendanceController::class, 'takeAttendance'])->name('attendance.create');
        Route::put('attendance/{session}', [TeacherAttendanceController::class, 'updateAttendance'])->name('attendance.update');
    });

    // Homework routes
    Route::middleware(['permission:homework.view', 'feature.access:homework'])->group(function () {
        Route::get('homeworks', [TeacherHomeworkController::class, 'index'])->name('homeworks.index');
        Route::get('homeworks/{homework}', [TeacherHomeworkController::class, 'show'])->name('homeworks.show');
        Route::post('homeworks/{homework}/submissions/{submission}/grade', [TeacherHomeworkController::class, 'grade'])->name('homeworks.submissions.grade');
    });

    // Analytics routes
    Route::get('analytics', [TeacherDashboardController::class, 'analytics'])->name('analytics');
    
    // Guidance routes
    Route::middleware(['feature.access:guidance'])->group(function () {
        Route::get('my-students', [\App\Http\Controllers\Teacher\TeacherGuidanceController::class, 'myStudents'])->name('guidance.students');
        Route::get('my-guidance', [\App\Http\Controllers\Teacher\TeacherGuidanceController::class, 'myGuidance'])->name('guidance.dashboard');
    });
    
    // Exam routes
    Route::middleware(['feature.access:exam'])->group(function () {
        Route::get('exams', [\App\Http\Controllers\Teacher\TeacherExamController::class, 'index'])->name('exams.index');
        Route::get('exams/{exam}', [\App\Http\Controllers\Teacher\TeacherExamController::class, 'show'])->name('exams.show');
        Route::get('exams/{exam}/results', [\App\Http\Controllers\Teacher\TeacherExamController::class, 'results'])->name('exams.results');
    });

    // Schedule routes
    Route::middleware(['feature.access:schedule'])->group(function () {
        Route::get('schedule', [\App\Http\Controllers\Teacher\TeacherScheduleController::class, 'index'])->name('schedule.index');
    });

    // Notification routes
    Route::middleware(['feature.access:notification'])->group(function () {
        Route::get('notifications', [\App\Http\Controllers\Teacher\TeacherNotificationController::class, 'index'])->name('notifications.index');
        Route::get('notifications/create', [\App\Http\Controllers\Teacher\TeacherNotificationController::class, 'create'])->name('notifications.create');
        Route::post('notifications', [\App\Http\Controllers\Teacher\TeacherNotificationController::class, 'store'])->name('notifications.store');
        Route::post('notifications/{notification}/read', [\App\Http\Controllers\Teacher\TeacherNotificationController::class, 'markAsRead'])->name('notifications.read');
    });
});
