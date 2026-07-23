<?php

namespace App\Domain\Reporting\Services;

use App\Models\Student;
use App\Models\Teacher;
use App\Models\Branch;
use App\Models\ClassSchedule;
use App\Models\AttendanceSession;
use App\Models\Attendance;
use App\Models\AttendanceStatus;
use App\Models\ExamResult;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\AssignmentSubmission;
use App\Models\Notification;
use App\Models\DashboardSnapshot;
use App\Core\Repositories\Interfaces\ReportingRepositoryInterface;

class ExecutiveDashboardService
{
    public function __construct(
        protected AnalyticsCacheService $cacheService,
        protected ReportingRepositoryInterface $repo
    ) {}

    public function getMetrics(): array
    {
        $cached = $this->cacheService->get('executive_dashboard_metrics');
        if ($cached) {
            return $cached;
        }

        // Live calculation using optimized queries
        $studentCount = Student::count();
        $teacherCount = Teacher::count();
        $branchCount = Branch::count();
        
        $todayDayOfWeek = (int) date('N');
        $todayLessons = ClassSchedule::where('day_of_week', $todayDayOfWeek)->where('is_active', true)->count();
        
        $todayAttendanceSessions = AttendanceSession::whereDate('session_date', date('Y-m-d'))->count();

        // Calculate unexcused absence rate
        $absentStatusId = AttendanceStatus::where('code', 'ABSENT')->value('id');
        $totalAttendanceCount = Attendance::count();
        $absentCount = $absentStatusId ? Attendance::where('attendance_status_id', $absentStatusId)->count() : 0;
        $absenceRate = $totalAttendanceCount > 0 ? round(($absentCount / $totalAttendanceCount) * 100, 1) : 0.0;

        // Exam Net averages
        $avgTytNet = ExamResult::whereHas('exam', function ($q) {
            $q->where('exam_type', 'TYT');
        })->avg('total_net') ?? 0.0;

        $avgAytNet = ExamResult::whereHas('exam', function ($q) {
            $q->where('exam_type', 'AYT');
        })->avg('total_net') ?? 0.0;

        // Finance
        $totalCollected = Payment::sum('amount') ?? 0.0;
        $totalInvoiced = Invoice::sum('total_amount') ?? 0.0;
        $totalPaidInvoices = Invoice::sum('paid_amount') ?? 0.0;
        $pendingDebt = max(0, $totalInvoiced - $totalPaidInvoices);

        // Homework
        $totalSubmissions = AssignmentSubmission::count();

        // Notification stats
        $totalNotifications = Notification::count();

        $metrics = [
            'student_count' => $studentCount,
            'teacher_count' => $teacherCount,
            'branch_count' => $branchCount,
            'today_lessons' => $todayLessons,
            'today_attendance_sessions' => $todayAttendanceSessions,
            'absence_rate' => $absenceRate,
            'avg_tyt_net' => round($avgTytNet, 2),
            'avg_ayt_net' => round($avgAytNet, 2),
            'total_collected' => round($totalCollected, 2),
            'pending_debt' => round($pendingDebt, 2),
            'total_submissions' => $totalSubmissions,
            'total_notifications' => $totalNotifications,
            'calculated_at' => now()->toDateTimeString(),
        ];

        // Store in analytics cache for 15 minutes
        $this->cacheService->put('executive_dashboard_metrics', $metrics, 15);

        return $metrics;
    }

    public function generateSnapshot(): DashboardSnapshot
    {
        $metrics = $this->getMetrics();
        return $this->repo->createSnapshot($metrics);
    }
}
