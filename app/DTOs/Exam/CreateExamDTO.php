<?php
namespace App\DTOs\Exam;

class CreateExamDTO
{
    public function __construct(
        public string $title,
        public string $code,
        public string $exam_type = 'Trial',
        public string $exam_date = '',
        public int $total_questions = 120,
        public int $duration_minutes = 135,
        public bool $is_published = true
    ) {}
}
