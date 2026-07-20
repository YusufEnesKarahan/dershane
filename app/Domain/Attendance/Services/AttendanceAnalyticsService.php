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
        $students = Student::with(['attendances.status'])->get();
        $riskStudents = collect();

        foreach ($students as $student) {
            $totalSessions = $student->attendances->count();
            if ($totalSessions === 0) continue;

            $absentCount = $student->attendances->filter(fn($a) => $a->status && $a->status->is_absence)->count();
            $absenceRate = ($absentCount / $totalSessions) * 100;

            if ($absenceRate >= $thresholdPercentage) {
                $student->absence_rate = round($absenceRate, 1);
                $student->absent_count = $absentCount;
                $student->total_sessions = $totalSessions;
                $riskStudents->push($student);
            }
        }

        return $riskStudents;
    }

    public function getSummary(): array
    {
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
    }
}
