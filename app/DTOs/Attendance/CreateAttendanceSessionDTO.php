<?php
namespace App\DTOs\Attendance;

class CreateAttendanceSessionDTO
{
    public function __construct(
        public int $classroom_id,
        public int $course_id,
        public int $teacher_id,
        public string $session_date,
        public string $start_time,
        public string $end_time,
        public ?int $class_schedule_id = null,
        public string $status = 'Completed'
    ) {}
}
