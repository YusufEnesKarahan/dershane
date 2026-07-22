<?php

namespace App\DTOs\Teacher;

class TeacherPerformanceDTO
{
    public function __construct(
        public int $teacher_id,
        public string $metric_type,
        public float $score,
        public ?string $comments = null
    ) {}
}
