<?php

namespace App\DTOs\Teacher;

class CreateTeacherDTO
{
    public function __construct(
        public int $user_id,
        public ?int $branch_id = null,
        public ?string $title = null,
        public ?string $bio = null,
        public ?string $specialties = null,
        public ?string $education = null,
        public int $experience_years = 0,
        public ?string $emergency_contact = null,
        public string $status = 'Active'
    ) {}
}
