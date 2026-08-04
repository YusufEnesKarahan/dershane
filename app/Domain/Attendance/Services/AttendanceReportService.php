<?php

namespace App\Domain\Attendance\Services;

use App\Models\AttendanceRecord;
use App\Models\AttendanceSession;
use Illuminate\Support\Facades\DB;

class AttendanceReportService
{
    public function getDailyAttendance(int $branchId, string $date)
    {
        return AttendanceSession::with(['classroom', 'teacher', 'records'])
            ->where('branch_id', $branchId)
            ->where('session_date', $date)
            ->get();
    }

    public function studentAttendanceSummary(int $branchId, int $studentId)
    {
        $records = AttendanceRecord::where('branch_id', $branchId)
            ->where('student_id', $studentId)
            ->get();

        $total = $records->count();
        if ($total === 0) {
            return [
                'total' => 0,
                'present' => 0,
                'absent' => 0,
                'late' => 0,
                'excused' => 0,
                'absence_rate' => 0
            ];
        }

        $summary = [
            'total' => $total,
            'present' => $records->where('status', 'present')->count(),
            'absent' => $records->where('status', 'absent')->count(),
            'late' => $records->where('status', 'late')->count(),
            'excused' => $records->where('status', 'excused')->count(),
        ];

        // Treat absent and late (partially) as absence for the rate?
        // Let's just use strict absence for rate.
        $summary['absence_rate'] = round(($summary['absent'] / $total) * 100, 2);

        return $summary;
    }

    public function classroomAttendanceReport(int $branchId, int $classroomId, $startDate, $endDate)
    {
        return AttendanceRecord::with(['student'])
            ->where('branch_id', $branchId)
            ->where('classroom_id', $classroomId)
            ->whereBetween('attendance_date', [$startDate, $endDate])
            ->get()
            ->groupBy('student_id');
    }

    public function monthlyAttendanceReport(int $branchId, int $year, int $month)
    {
        return AttendanceRecord::where('branch_id', $branchId)
            ->whereYear('attendance_date', $year)
            ->whereMonth('attendance_date', $month)
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status');
    }
}
