<?php
namespace App\DTOs\Course;

class CreateCourseDTO
{
    public function __construct(
        public string $code,
        public string $name,
        public ?string $description = null,
        public ?int $course_level_id = null,
        public ?string $duration = null,
        public int $capacity = 0,
        public string $status = 'Draft',
        public bool $is_active = true,
        public ?string $cover_image = null
    ) {}
}
