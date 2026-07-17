<?php
namespace App\DTOs\Teacher;

class TeacherPerformanceDTO
{
    public function __construct(
        public float $attendance_rate,
        public float $student_satisfaction,
        public int $lesson_count,
        public float $feedback_score,
        public string $kpi_month
    ) {}
}
