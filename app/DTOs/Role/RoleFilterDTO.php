<?php
namespace App\DTOs\Role;

class RoleFilterDTO
{
    public function __construct(public ?string $search = null) {}

    public static function fromRequest(array $data): self
    {
        return new self(search: $data['search'] ?? null);
    }
}
