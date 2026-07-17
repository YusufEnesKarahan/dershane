<?php
namespace App\DTOs\Teacher;

class TeacherFilterDTO
{
    public function __construct(
        public ?string $search = null,
        public ?int $branch_id = null,
        public ?string $status = null
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            $data['search'] ?? null,
            isset($data['branch_id']) ? (int) $data['branch_id'] : null,
            $data['status'] ?? null
        );
    }
}
