<?php
namespace App\Policies;

use App\Models\User;
use App\Models\Registration;
use App\Domain\Auth\Services\AuthorizationService;

class RegistrationPolicy
{
    public function __construct(protected AuthorizationService $authService) {}

    public function viewAny(User $user): bool
    {
        return $this->authService->hasPermission($user, 'registrations.view');
    }

    public function view(User $user, Registration $model): bool
    {
        return $this->authService->hasPermission($user, 'registrations.view');
    }

    public function create(User $user): bool
    {
        return $this->authService->hasPermission($user, 'registrations.create');
    }

    public function update(User $user, Registration $model): bool
    {
        return $this->authService->hasPermission($user, 'registrations.update');
    }

    public function delete(User $user, Registration $model): bool
    {
        return $this->authService->hasPermission($user, 'registrations.delete');
    }
}
