<?php
namespace App\DTOs\Teacher;

class TeacherLeaveDTO
{
    public function __construct(
        public string $start_date,
        public string $end_date,
        public ?string $reason = null
    ) {}
}
