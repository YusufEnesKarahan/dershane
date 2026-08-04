<?php

namespace App\Domain\Attendance\Services;

use App\Models\AttendanceRecord;
use App\Models\AttendanceSession;
use App\Models\Branch;
use Illuminate\Support\Facades\DB;

class AttendanceReportService
{
    protected function resolveBranchId(?int $branchId): ?int
    {
        return $branchId && $branchId > 0 
            ? $branchId 
            : (\App\Core\Context\TenantContext::getActiveBranchId() ?? session('active_branch_id') ?? auth()->user()?->branch_id ?? Branch::value('id'));
    }

    public function getDailyAttendance(?int $branchId, string $date)
    {
        $resolvedId = $this->resolveBranchId($branchId);
        $query = AttendanceSession::with(['classroom', 'teacher', 'records'])
            ->where('session_date', $date);

        if ($resolvedId) {
            $query->where('branch_id', $resolvedId);
        }

        return $query->get();
    }

    public function studentAttendanceSummary(?int $branchId, int $studentId)
    {
        $resolvedId = $this->resolveBranchId($branchId);
        $query = AttendanceRecord::where('student_id', $studentId);

        if ($resolvedId) {
            $query->where('branch_id', $resolvedId);
        }

        $records = $query->get();

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

        $summary['absence_rate'] = round(($summary['absent'] / $total) * 100, 2);

        return $summary;
    }

    public function classroomAttendanceReport(?int $branchId, int $classroomId, $startDate, $endDate)
    {
        $resolvedId = $this->resolveBranchId($branchId);
        $query = AttendanceRecord::with(['student'])
            ->where('classroom_id', $classroomId)
            ->whereBetween('attendance_date', [$startDate, $endDate]);

        if ($resolvedId) {
            $query->where('branch_id', $resolvedId);
        }

        return $query->get()->groupBy('student_id');
    }

    public function monthlyAttendanceReport(?int $branchId, int $year, int $month)
    {
        $resolvedId = $this->resolveBranchId($branchId);
        $query = AttendanceRecord::whereYear('attendance_date', $year)
            ->whereMonth('attendance_date', $month);

        if ($resolvedId) {
            $query->where('branch_id', $resolvedId);
        }

        return $query->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status');
    }
}
