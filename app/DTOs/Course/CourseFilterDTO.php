<?php
namespace App\DTOs\Course;

class CourseFilterDTO
{
    public function __construct(
        public ?string $search = null,
        public ?int $level_id = null
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            $data['search'] ?? null,
            isset($data['level_id']) ? (int) $data['level_id'] : null
        );
    }
}
