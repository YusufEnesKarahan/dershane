<?php
namespace App\DTOs\Exam;

class BulkExamResultDTO
{
    public function __construct(
        public int $exam_id,
        public array $results // Array of ExamResultDTO
    ) {}
}
