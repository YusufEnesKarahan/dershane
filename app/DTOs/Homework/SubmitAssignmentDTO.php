<?php
namespace App\DTOs\Homework;

class SubmitAssignmentDTO
{
    public function __construct(
        public int $assignment_id,
        public int $student_id,
        public ?string $remarks = null,
        public array $files = []
    ) {}
}
