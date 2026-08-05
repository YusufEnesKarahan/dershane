<?php

namespace App\Domain\Reporting\Services;

use App\Models\Student;
use App\Models\Teacher;
use App\Models\Branch;
use App\Models\ClassSchedule;
use App\Models\AttendanceSession;
use App\Models\ExamResult;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\HomeworkSubmission;
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
        return \Illuminate\Support\Facades\Cache::remember('executive_dashboard_metrics', 300, function () {
            // Live calculation using optimized queries
            $studentCount = Student::count();
            $teacherCount = Teacher::count();
            $branchCount = Branch::count();
            
            $todayDayOfWeek = (int) date('N');
            $todayLessons = ClassSchedule::where('day_of_week', $todayDayOfWeek)->where('is_active', true)->count();
            
            $todayAttendanceSessions = AttendanceSession::whereDate('session_date', date('Y-m-d'))->count();

            // Calculate absence rate
            $totalAttendanceCount = \App\Models\AttendanceRecord::count();
            $absentCount = \App\Models\AttendanceRecord::whereIn('status', ['absent', 'A', 'yok'])->count();
            $absenceRate = $totalAttendanceCount > 0 ? round(($absentCount / $totalAttendanceCount) * 100, 1) : 0.0;

            // Practice Exam Net averages (TYT, AYT, LGS, YKS)
            $avgTytNet = ExamResult::whereHas('exam', function ($q) {
                $q->where('type', 'TYT');
            })->avg('total_net') ?? 0.0;

            $avgAytNet = ExamResult::whereHas('exam', function ($q) {
                $q->where('type', 'AYT');
            })->avg('total_net') ?? 0.0;

            $avgLgsNet = ExamResult::whereHas('exam', function ($q) {
                $q->where('type', 'LGS');
            })->avg('total_net') ?? 0.0;

            $avgYksNet = ExamResult::whereHas('exam', function ($q) {
                $q->whereIn('type', ['YKS', 'Kurumsal Deneme']);
            })->avg('total_net') ?? 0.0;

            // Study Program Completion %
            $totalHomeworkSubmissions = HomeworkSubmission::count();
            $completedSubmissions = HomeworkSubmission::whereIn('task_status', ['Completed', 'graded', 'submitted'])->count();
            $studyProgramCompletionRate = $totalHomeworkSubmissions > 0 ? round(($completedSubmissions / $totalHomeworkSubmissions) * 100, 1) : 0.0;

            // Finance
            $totalCollected = Payment::sum('amount') ?? 0.0;
            $totalInvoiced = Invoice::sum('total_amount') ?? 0.0;
            $totalPaidInvoices = Invoice::sum('paid_amount') ?? 0.0;
            $pendingDebt = max(0, $totalInvoiced - $totalPaidInvoices);

            // Notification stats
            $totalNotifications = Notification::count();

            return [
                'student_count' => $studentCount,
                'teacher_count' => $teacherCount,
                'branch_count' => $branchCount,
                'today_lessons' => $todayLessons,
                'today_attendance_sessions' => $todayAttendanceSessions,
                'absence_rate' => $absenceRate,
                'avg_tyt_net' => round($avgTytNet, 2),
                'avg_ayt_net' => round($avgAytNet, 2),
                'avg_lgs_net' => round($avgLgsNet, 2),
                'avg_yks_net' => round($avgYksNet, 2),
                'study_program_completion_rate' => $studyProgramCompletionRate,
                'total_collected' => round($totalCollected, 2),
                'pending_debt' => round($pendingDebt, 2),
                'total_submissions' => $totalHomeworkSubmissions,
                'total_notifications' => $totalNotifications,
                'calculated_at' => now()->toDateTimeString(),
            ];
        });
    }

    public function generateSnapshot(): DashboardSnapshot
    {
        $metrics = $this->getMetrics();
        return $this->repo->createSnapshot($metrics);
    }
}
