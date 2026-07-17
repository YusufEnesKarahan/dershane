<?php
namespace App\Domain\Teacher\Services;

use App\Models\Teacher;

class TeacherAnalyticsService
{
    public function getAnalyticsSummary(Teacher $teacher): array
    {
        return [
            'lesson_count' => $teacher->schedules()->count(),
            'student_satisfaction' => $teacher->performances()->avg('student_satisfaction') ?: 5.0,
            'attendance_rate' => $teacher->performances()->avg('attendance_rate') ?: 100.0,
        ];
    }
}
