<?php
namespace App\DTOs\Teacher;

class TeacherScheduleDTO
{
    public function __construct(
        public int $teacher_id,
        public ?int $classroom_id = null,
        public ?int $course_id = null,
        public string $date,
        public string $start_time,
        public string $end_time
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            (int) $data['teacher_id'],
            isset($data['classroom_id']) ? (int) $data['classroom_id'] : null,
            isset($data['course_id']) ? (int) $data['course_id'] : null,
            $data['date'] ?? '',
            $data['start_time'] ?? '',
            $data['end_time'] ?? ''
        );
    }
}
