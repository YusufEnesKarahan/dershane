<?php
namespace App\DTOs\User;

class CreateUserDTO
{
    public function __construct(
        public string $name,
        public string $email,
        public string $password,
        public ?string $phone = null,
        public string $status = 'ACTIVE',
        public ?int $branch_id = null,
        public array $roles = [],
        public array $preferences = []
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            name: $data['name'],
            email: $data['email'],
            password: $data['password'],
            phone: $data['phone'] ?? null,
            status: $data['status'] ?? 'ACTIVE',
            branch_id: $data['branch_id'] ? (int) $data['branch_id'] : null,
            roles: $data['roles'] ?? [],
            preferences: $data['preferences'] ?? []
        );
    }
}
