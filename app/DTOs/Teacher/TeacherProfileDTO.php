<?php
namespace App\DTOs\Teacher;

class TeacherProfileDTO
{
    public function __construct(
        public string $bio,
        public array $skills = [],
        public array $languages = []
    ) {}
}
