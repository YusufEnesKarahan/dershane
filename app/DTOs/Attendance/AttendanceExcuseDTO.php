<?php
namespace App\DTOs\Attendance;

class AttendanceExcuseDTO
{
    public function __construct(
        public int $attendance_id,
        public int $student_id,
        public string $excuse_reason,
        public ?string $document_path = null
    ) {}
}
