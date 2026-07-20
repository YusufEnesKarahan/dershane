<?php
namespace App\DTOs\Student;

class GuardianDTO
{
    public function __construct(
        public int $student_id,
        public string $guardian_name,
        public string $relation,
        public string $phone,
        public ?string $email = null,
        public bool $is_primary = true
    ) {}
}
