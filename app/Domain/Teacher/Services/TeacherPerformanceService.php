<?php
namespace App\Domain\Teacher\Services;

use App\Models\Teacher;
use App\Models\TeacherPerformance;

class TeacherPerformanceService
{
    public function logPerformance(Teacher $teacher, array $metrics): TeacherPerformance
    {
        return TeacherPerformance::create([
            'teacher_id' => $teacher->id,
            'attendance_rate' => $metrics['attendance_rate'] ?? 100.0,
            'student_satisfaction' => $metrics['student_satisfaction'] ?? 5.0,
            'lesson_count' => $metrics['lesson_count'] ?? 0,
            'feedback_score' => $metrics['feedback_score'] ?? 5.0,
            'kpi_month' => $metrics['kpi_month'] ?? now()->format('Y-m'),
        ]);
    }
}
