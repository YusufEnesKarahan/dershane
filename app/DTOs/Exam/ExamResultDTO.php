<?php
namespace App\DTOs\Exam;

class ExamResultDTO
{
    public function __construct(
        public int $exam_id,
        public int $student_id,
        public int $total_correct = 0,
        public int $total_wrong = 0,
        public int $total_empty = 0,
        public bool $is_absent = false,
        public array $subject_breakdown = [] // ['subject_name' => '', 'correct' => int, 'wrong' => int, 'empty' => int]
    ) {}
}
