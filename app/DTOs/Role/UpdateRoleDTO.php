<?php
namespace App\DTOs\Role;

class UpdateRoleDTO
{
    public function __construct(
        public string $name,
        public ?string $description = null,
        public ?string $color = null,
        public array $permissions = []
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            name: $data['name'],
            description: $data['description'] ?? null,
            color: $data['color'] ?? null,
            permissions: $data['permissions'] ?? []
        );
    }
}
