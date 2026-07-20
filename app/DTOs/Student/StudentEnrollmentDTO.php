<?php
namespace App\DTOs\Student;

class StudentEnrollmentDTO
{
    public function __construct(
        public int $student_id,
        public int $course_id,
        public ?int $academic_term_id = null,
        public float $price_paid = 0.0,
        public ?string $enrollment_date = null
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            (int) $data['student_id'],
            (int) $data['course_id'],
            isset($data['academic_term_id']) ? (int) $data['academic_term_id'] : null,
            (float) ($data['price_paid'] ?? 0),
            $data['enrollment_date'] ?? date('Y-m-d')
        );
    }
}
