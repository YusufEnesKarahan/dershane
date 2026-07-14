<?php
namespace App\Policies;

use App\Models\User;
use App\Models\Role;
use App\Domain\Auth\Services\AuthorizationService;

class RolePolicy
{
    public function __construct(protected AuthorizationService $authService) {}

    public function viewAny(User $user): bool
    {
        return $this->authService->hasPermission($user, 'roles.view');
    }

    public function view(User $user, Role $model): bool
    {
        return $this->authService->hasPermission($user, 'roles.view');
    }

    public function create(User $user): bool
    {
        return $this->authService->hasPermission($user, 'roles.create');
    }

    public function update(User $user, Role $model): bool
    {
        return $this->authService->hasPermission($user, 'roles.update');
    }

    public function delete(User $user, Role $model): bool
    {
        return $this->authService->hasPermission($user, 'roles.delete');
    }
}
