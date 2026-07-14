<?php
namespace App\DTOs\User;

class UserFilterDTO
{
    public function __construct(
        public ?string $search = null,
        public ?string $role = null,
        public ?int $branch_id = null,
        public ?string $status = null
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            search: $data['search'] ?? null,
            role: $data['role'] ?? null,
            branch_id: ($data['branch_id'] ?? null) ? (int) $data['branch_id'] : null,
            status: $data['status'] ?? null
        );
    }
}
