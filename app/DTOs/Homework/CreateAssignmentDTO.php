<?php
namespace App\DTOs\Homework;

class CreateAssignmentDTO
{
    public function __construct(
        public string $title,
        public string $code,
        public int $teacher_id,
        public string $due_date,
        public ?string $description = null,
        public string $assignment_type = 'Classroom',
        public ?int $classroom_id = null,
        public ?int $course_id = null,
        public int $max_score = 100,
        public string $status = 'Published'
    ) {}
}
