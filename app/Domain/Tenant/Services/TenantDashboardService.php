<?php

namespace App\Domain\Tenant\Services;

use App\Models\Student;
use App\Models\Teacher;
use App\Models\Classroom;
use App\Models\Attendance;
use App\Models\AttendanceSession;
use App\Models\ExamSession;
use App\Models\PlatformAuditLog;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class TenantDashboardService
{
    /**
     * Get all dashboard metrics with caching
     */
    public function getDashboardData(int $branchId)
    {
        $cacheKey = "dashboard_metrics_{$branchId}";
        
        return Cache::remember($cacheKey, now()->addMinutes(5), function () use ($branchId) {
            return [
                'statistics' => $this->getStatistics($branchId),
                'student_growth' => $this->getStudentGrowth($branchId),
                'attendance_summary' => $this->getAttendanceSummary($branchId),
                'recent_activities' => $this->getRecentActivities($branchId),
            ];
        });
    }

    /**
     * Clear dashboard cache for a branch
     */
    public function clearCache(int $branchId)
    {
        Cache::forget("dashboard_metrics_{$branchId}");
    }

    /**
     * Get basic statistics for KPI cards
     */
    private function getStatistics(int $branchId): array
    {
        // Total active students
        $totalStudents = Student::where('branch_id', $branchId)->where('status', 'active')->count();

        // Total active teachers
        $totalTeachers = Teacher::where('branch_id', $branchId)->where('status', 'active')->count();

        // Total active classrooms
        $totalClassrooms = Classroom::where('branch_id', $branchId)->where('status', 'active')->count();

        // Today's attendance
        $today = Carbon::today();
        
        // Find today's attendance sessions
        $todaySessionIds = AttendanceSession::whereDate('session_date', $today)->pluck('id');
        
        $attendanceStats = [
            'present' => 0,
            'absent' => 0,
        ];
        
        if ($todaySessionIds->isNotEmpty()) {
            $attendances = Attendance::whereIn('attendance_session_id', $todaySessionIds)
                ->with('status')
                ->get();
                
            foreach ($attendances as $attendance) {
                if ($attendance->status && $attendance->status->is_absence) {
                    $attendanceStats['absent']++;
                } else {
                    $attendanceStats['present']++;
                }
            }
        }

        // Upcoming exams
        $upcomingExams = ExamSession::where('branch_id', $branchId)
            ->whereDate('session_date', '>=', $today)
            ->count();

        return [
            'students' => $totalStudents,
            'teachers' => $totalTeachers,
            'classrooms' => $totalClassrooms,
            'attendance' => $attendanceStats,
            'upcoming_exams' => $upcomingExams,
        ];
    }

    /**
     * Get student growth for the last 6 months
     */
    private function getStudentGrowth(int $branchId): array
    {
        $growth = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            
            // Assuming enrollment date or created_at for student growth
            $count = Student::where('status', 'active')
                ->whereYear('created_at', $month->year)
                ->whereMonth('created_at', '<=', $month->month)
                ->count();
                
            $growth[] = [
                'month' => $month->translatedFormat('M y'),
                'count' => $count
            ];
        }
        
        return $growth;
    }

    /**
     * Get attendance summary for the last 30 days
     */
    private function getAttendanceSummary(int $branchId): array
    {
        $startDate = Carbon::now()->subDays(30);
        $endDate = Carbon::now();
        
        $sessions = AttendanceSession::whereBetween('session_date', [$startDate, $endDate])
            ->pluck('id');
            
        if ($sessions->isEmpty()) {
            return [
                'present' => 0,
                'absent' => 0
            ];
        }
        
        $attendances = Attendance::whereIn('attendance_session_id', $sessions)
            ->with('status')
            ->get();
            
        $present = 0;
        $absent = 0;
        
        foreach ($attendances as $attendance) {
            if ($attendance->status && $attendance->status->is_absence) {
                $absent++;
            } else {
                $present++;
            }
        }
        
        return [
            'present' => $present,
            'absent' => $absent
        ];
    }

    /**
     * Get recent activities from Audit logs or custom events
     */
    private function getRecentActivities(int $branchId): \Illuminate\Support\Collection
    {
        // Try to fetch from PlatformAuditLog if available.
        // Assuming user relations and branch_id exist via user, or we fallback.
        // Since PlatformAuditLog has user_id, we can join users to filter by branch_id.
        return PlatformAuditLog::select('platform_audit_logs.*')
            ->join('users', 'users.id', '=', 'platform_audit_logs.user_id')
            ->where('users.branch_id', $branchId)
            ->orderBy('platform_audit_logs.created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($log) {
                return $this->formatAuditLogMessage($log);
            });
    }

    private function formatAuditLogMessage($log)
    {
        $action = $log->action ?? 'İşlem';
        $target = class_basename($log->target_type);
        $time = $log->created_at->diffForHumans();
        
        // Translate actions roughly
        $actionTr = match($action) {
            'created' => 'oluşturuldu',
            'updated' => 'güncellendi',
            'deleted' => 'silindi',
            default => 'işlem yapıldı',
        };
        
        // Translate target roughly
        $targetTr = match($target) {
            'Student' => 'Öğrenci',
            'Teacher' => 'Öğretmen',
            'Classroom' => 'Sınıf',
            default => $target,
        };

        return [
            'message' => "Yeni bir {$targetTr} {$actionTr}.",
            'time' => $time,
            'user' => $log->user->name ?? 'Sistem',
            'icon' => $action === 'created' ? 'plus-circle' : ($action === 'deleted' ? 'trash' : 'edit'),
            'color' => $action === 'created' ? 'text-emerald-500' : ($action === 'deleted' ? 'text-rose-500' : 'text-blue-500')
        ];
    }
}
