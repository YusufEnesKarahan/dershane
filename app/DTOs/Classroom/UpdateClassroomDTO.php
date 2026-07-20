<?php
namespace App\DTOs\Classroom;

class UpdateClassroomDTO
{
    public function __construct(
        public string $name,
        public ?int $branch_id = null,
        public ?int $classroom_type_id = null,
        public int $capacity = 30,
        public string $color_code = '#4F46E5',
        public bool $is_active = true
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            $data['name'] ?? '',
            isset($data['branch_id']) ? (int) $data['branch_id'] : null,
            isset($data['classroom_type_id']) ? (int) $data['classroom_type_id'] : null,
            (int) ($data['capacity'] ?? 30),
            $data['color_code'] ?? '#4F46E5',
            (bool) ($data['is_active'] ?? true)
        );
    }
}
