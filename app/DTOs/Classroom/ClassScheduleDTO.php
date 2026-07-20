<?php
namespace App\DTOs\Classroom;

class ClassScheduleDTO
{
    public function __construct(
        public int $classroom_id,
        public int $teacher_id,
        public int $course_id,
        public ?int $academic_term_id,
        public int $day_of_week,
        public string $start_time,
        public string $end_time,
        public string $color_code = '#3B82F6',
        public bool $is_active = true
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            (int) $data['classroom_id'],
            (int) $data['teacher_id'],
            (int) $data['course_id'],
            isset($data['academic_term_id']) ? (int) $data['academic_term_id'] : null,
            (int) $data['day_of_week'],
            $data['start_time'],
            $data['end_time'],
            $data['color_code'] ?? '#3B82F6',
            (bool) ($data['is_active'] ?? true)
        );
    }
}
