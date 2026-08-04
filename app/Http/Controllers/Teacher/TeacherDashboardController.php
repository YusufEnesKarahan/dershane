<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Domain\Teacher\Services\TeacherPortalService;
use App\Domain\Teacher\Services\TeacherScheduleService;
use App\Domain\Teacher\Services\TeacherAnalyticsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TeacherDashboardController extends Controller
{
    public function __construct(
        protected TeacherPortalService $portalService,
        protected TeacherScheduleService $scheduleService,
        protected TeacherAnalyticsService $analyticsService,
        protected \App\Domain\Dashboard\Services\DashboardService $dashboardService
    ) {}

    public function index()
    {
        $user = Auth::user();
        $teacher = $this->portalService->getTeacherByUserId($user->id);
        if (!$teacher && $user?->hasRole('Super Admin')) {
            $teacher = \App\Models\Teacher::first();
        }

        if (!$teacher) {
            return redirect()->route('admin.dashboard')->with('error', 'Öğretmen hesabı bulunamadı.');
        }

        $assignedClasses = $this->portalService->getAssignedClasses($teacher->id);
        $schedules = $this->scheduleService->getSchedules($teacher->id);
        $analytics = $this->analyticsService->getAnalyticsSummary($teacher->id);
        $dashboardData = $this->dashboardService->getTeacherDashboardData($user);

        return view('teacher.dashboard', array_merge(compact('teacher', 'assignedClasses', 'schedules', 'analytics'), $dashboardData));
    }

    public function analytics()
    {
        $user = Auth::user();
        $teacher = $this->portalService->getTeacherByUserId($user->id);
        if (!$teacher && $user?->hasRole('Super Admin')) {
            $teacher = \App\Models\Teacher::first();
        }
        if (!$teacher) {
            return redirect()->back();
        }

        $analytics = $this->analyticsService->getAnalyticsSummary($teacher->id);
        $performanceLogs = \App\Models\TeacherPerformanceLog::where('teacher_id', $teacher->id)->orderBy('evaluated_at', 'desc')->get();

        return view('teacher.analytics', compact('teacher', 'analytics', 'performanceLogs'));
    }
}
