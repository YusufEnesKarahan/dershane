<?php
namespace App\DTOs\Student;

class CreateStudentDTO
{
    public function __construct(
        public string $student_number,
        public string $first_name,
        public string $last_name,
        public int $branch_id,
        public ?string $identity_number = null,
        public ?string $birth_date = null,
        public ?string $gender = null,
        public ?int $classroom_id = null,
        public string $status = 'Active'
    ) {}
}
