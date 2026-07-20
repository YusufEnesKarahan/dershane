<?php
namespace App\DTOs\Course;

class UpdateCourseDTO
{
    public function __construct(
        public string $name,
        public ?string $description = null,
        public ?int $course_level_id = null,
        public ?string $duration = null,
        public int $capacity = 0,
        public string $status = 'Draft',
        public bool $is_active = true,
        public ?string $cover_image = null,
        public array $teachers = [],
        public array $branches = [],
        public array $prerequisites = []
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            $data['name'] ?? '',
            $data['description'] ?? null,
            isset($data['course_level_id']) ? (int) $data['course_level_id'] : null,
            $data['duration'] ?? null,
            (int) ($data['capacity'] ?? 0),
            $data['status'] ?? 'Draft',
            (bool) ($data['is_active'] ?? true),
            $data['cover_image'] ?? null,
            $data['teachers'] ?? [],
            $data['branches'] ?? [],
            $data['prerequisites'] ?? []
        );
    }
}
