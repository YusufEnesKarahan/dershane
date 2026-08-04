<?php

namespace App\Domain\UserManagement\Actions;

use App\Domain\UserManagement\Services\UserManagementService;
use App\Models\User;
use App\DTOs\User\UpdateUserDTO;

class UpdateUserAction
{
    public function __construct(
        protected UserManagementService $service
    ) {}

    public function execute(User $user, UpdateUserDTO|array $data): User
    {
        $payload = $data instanceof UpdateUserDTO ? $data->toArray() : $data;
        return $this->service->updateUser($user, $payload);
    }
}
