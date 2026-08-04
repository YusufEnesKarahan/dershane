<?php

namespace App\Domain\Guidance\Services;

use App\Models\ParentMeeting;
use App\Models\PerformanceSnapshot;

class ParentGuidanceService
{
    public function getUpcomingMeetings(int $guardianId, int $branchId)
    {
        return ParentMeeting::with(['student', 'teacher.user'])
            ->where('branch_id', $branchId)
            ->where('guardian_id', $guardianId)
            ->where('meeting_date', '>=', now())
            ->orderBy('meeting_date', 'asc')
            ->get();
    }

    public function getChildPerformance(int $studentId, int $branchId)
    {
        return PerformanceSnapshot::where('branch_id', $branchId)
            ->where('student_id', $studentId)
            ->orderBy('snapshot_date', 'desc')
            ->first();
    }
}
