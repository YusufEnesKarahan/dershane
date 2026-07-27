<?php
namespace App\Domain\Attendance\Services;

use App\Models\Attendance;
use App\Models\AttendanceSession;
use App\Models\Student;
use Illuminate\Support\Collection;

class AttendanceAnalyticsService
{
    public function getRiskStudents(float $thresholdPercentage = 15.0): Collection
    {
        return Student::select('id', 'first_name', 'last_name', 'student_number')
            ->withCount([
                'attendances as total_sessions',
                'attendances as absent_count' => function ($query) {
                    $query->whereHas('status', fn($q) => $q->where('is_absence', true));
                }
            ])
            ->get()
            ->filter(function ($student) use ($thresholdPercentage) {
                if ($student->total_sessions === 0) return false;
                $absenceRate = ($student->absent_count / $student->total_sessions) * 100;
                $student->absence_rate = round($absenceRate, 1);
                return $absenceRate >= $thresholdPercentage;
            })
            ->values();
    }

    public function getSummary(): array
    {
        return \Illuminate\Support\Facades\Cache::remember('attendance.analytics.summary', 600, function () {
            $totalSessions = AttendanceSession::count();
            $totalAttendances = Attendance::count();
            $absenceAttendances = Attendance::whereHas('status', fn($q) => $q->where('is_absence', true))->count();

            $overallRate = $totalAttendances > 0 ? round((($totalAttendances - $absenceAttendances) / $totalAttendances) * 100, 1) : 100;

            return [
                'total_sessions' => $totalSessions,
                'total_attendances' => $totalAttendances,
                'overall_attendance_rate' => $overallRate,
                'risk_students_count' => $this->getRiskStudents()->count(),
            ];
        });
    }
}
