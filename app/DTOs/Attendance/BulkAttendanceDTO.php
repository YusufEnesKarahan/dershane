<?php
namespace App\DTOs\Attendance;

class BulkAttendanceDTO
{
    public function __construct(
        public int $attendance_session_id,
        public array $attendances // Array of ['student_id' => int, 'attendance_status_id' => int, 'remarks' => string|null]
    ) {}
}
