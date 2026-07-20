<?php
namespace App\Domain\Attendance\Services;

use App\Models\AttendanceSession;

class AttendanceReportService
{
    public function getDailyReport(string $date): array
    {
        $sessions = AttendanceSession::with(['classroom', 'course', 'teacher.user', 'attendances.status'])
            ->where('session_date', $date)
            ->get();

        return [
            'date' => $date,
            'total_sessions' => $sessions->count(),
            'sessions' => $sessions,
        ];
    }
}
