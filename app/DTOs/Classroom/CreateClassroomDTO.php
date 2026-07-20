<?php
namespace App\DTOs\Classroom;

class CreateClassroomDTO
{
    public function __construct(
        public string $code,
        public string $name,
        public ?int $branch_id = null,
        public ?int $classroom_type_id = null,
        public int $capacity = 30,
        public string $color_code = '#4F46E5',
        public bool $is_active = true
    ) {}
}
