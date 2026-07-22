<?php

namespace App\Domain\Teacher\Services;

use App\Models\Teacher;
use App\Models\TeacherAssignment;
use App\Models\TeacherPerformanceLog;
use App\Models\Attendance;
use App\Models\ExamResult;

class TeacherAnalyticsService
{
    public function getAnalyticsSummary(int $teacherId): array
    {
        $teacher = Teacher::findOrFail($teacherId);
        $assignedClassesCount = TeacherAssignment::where('teacher_id', $teacherId)->count();

        // Calculate Average Performance Score
        $averagePerformance = TeacherPerformanceLog::where('teacher_id', $teacherId)->avg('score');

        return [
            'teacher' => $teacher,
            'assigned_classes_count' => $assignedClassesCount,
            'average_performance_score' => $averagePerformance ? round($averagePerformance, 1) : 100.0,
        ];
    }
}
