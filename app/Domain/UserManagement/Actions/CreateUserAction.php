<?php

namespace App\Domain\UserManagement\Actions;

use App\Domain\UserManagement\Services\UserManagementService;
use App\Models\User;
use App\DTOs\User\CreateUserDTO;

class CreateUserAction
{
    public function __construct(
        protected UserManagementService $service
    ) {}

    public function execute(CreateUserDTO|array $data): User
    {
        $payload = $data instanceof CreateUserDTO ? $data->toArray() : $data;
        return $this->service->createUser($payload);
    }
}
