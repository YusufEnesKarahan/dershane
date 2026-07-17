<?php
namespace App\DTOs\Teacher;

class TeacherAvailabilityDTO
{
    public function __construct(
        public int $teacher_id,
        public int $day_of_week,
        public string $start_time,
        public string $end_time,
        public bool $is_recurring = true
    ) {}
}
