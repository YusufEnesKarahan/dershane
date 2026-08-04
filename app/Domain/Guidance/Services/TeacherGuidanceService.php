<?php

namespace App\Domain\Guidance\Services;

use App\Models\StudentGuidanceRecord;
use App\Models\StudentMeeting;
use App\Models\StudentRiskLevel;
use App\Models\Student;

class TeacherGuidanceService
{
    public function getStudentsNeedingAttention(int $teacherId, int $branchId)
    {
        return Student::with('riskLevels', 'user')
            ->where('branch_id', $branchId)
            ->whereHas('riskLevels', function($q) {
                $q->whereIn('level', ['High', 'Critical']);
            })->get();
    }

    public function getUpcomingMeetings(int $teacherId, int $branchId)
    {
        return StudentMeeting::with('student.user')
            ->where('branch_id', $branchId)
            ->where('teacher_id', $teacherId)
            ->where('meeting_date', '>=', now())
            ->orderBy('meeting_date', 'asc')
            ->limit(10)
            ->get();
    }
}
