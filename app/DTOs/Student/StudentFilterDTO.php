<?php
namespace App\DTOs\Student;

class StudentFilterDTO
{
    public function __construct(
        public ?string $search = null,
        public ?string $status = null
    ) {}
}
