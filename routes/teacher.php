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
    Route::get('attendance', [TeacherAttendanceController::class, 'myClasses'])
        ->middleware('permission:attendance.view')
        ->name('attendance.index');
    Route::get('attendance/{session}', [TeacherAttendanceController::class, 'takeAttendance'])
        ->middleware('permission:attendance.update')
        ->name('attendance.create');
    Route::put('attendance/{session}', [TeacherAttendanceController::class, 'updateAttendance'])
        ->middleware('permission:attendance.update')
        ->name('attendance.update');

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
    
    // Guidance routes
    Route::get('my-students', [\App\Http\Controllers\Teacher\TeacherGuidanceController::class, 'myStudents'])->name('guidance.students');
    Route::get('my-guidance', [\App\Http\Controllers\Teacher\TeacherGuidanceController::class, 'myGuidance'])->name('guidance.dashboard');
    
    // Exam routes
    Route::get('exams', [\App\Http\Controllers\Teacher\TeacherExamController::class, 'index'])->name('exams.index');
    Route::get('exams/{exam}', [\App\Http\Controllers\Teacher\TeacherExamController::class, 'show'])->name('exams.show');
    Route::get('exams/{exam}/results', [\App\Http\Controllers\Teacher\TeacherExamController::class, 'results'])->name('exams.results');
    Route::post('exams/{exam}/results', [\App\Http\Controllers\Teacher\TeacherExamController::class, 'storeResult'])->name('exams.results.store');
});
