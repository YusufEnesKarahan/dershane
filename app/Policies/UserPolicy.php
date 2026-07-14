<?php
namespace App\Policies;

use App\Models\User;
use App\Models\User;
use App\Domain\Auth\Services\AuthorizationService;

class UserPolicy
{
    public function __construct(protected AuthorizationService $authService) {}

    public function viewAny(User $user): bool
    {
        return $this->authService->hasPermission($user, 'users.view');
    }

    public function view(User $user, User $model): bool
    {
        return $this->authService->hasPermission($user, 'users.view');
    }

    public function create(User $user): bool
    {
        return $this->authService->hasPermission($user, 'users.create');
    }

    public function update(User $user, User $model): bool
    {
        return $this->authService->hasPermission($user, 'users.update');
    }

    public function delete(User $user, User $model): bool
    {
        return $this->authService->hasPermission($user, 'users.delete');
    }
}
