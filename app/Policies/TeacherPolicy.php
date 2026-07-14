<?php
namespace App\Policies;

use App\Models\User;
use App\Models\Teacher;
use App\Domain\Auth\Services\AuthorizationService;

class TeacherPolicy
{
    public function __construct(protected AuthorizationService $authService) {}

    public function viewAny(User $user): bool
    {
        return $this->authService->hasPermission($user, 'teachers.view');
    }

    public function view(User $user, Teacher $model): bool
    {
        return $this->authService->hasPermission($user, 'teachers.view');
    }

    public function create(User $user): bool
    {
        return $this->authService->hasPermission($user, 'teachers.create');
    }

    public function update(User $user, Teacher $model): bool
    {
        return $this->authService->hasPermission($user, 'teachers.update');
    }

    public function delete(User $user, Teacher $model): bool
    {
        return $this->authService->hasPermission($user, 'teachers.delete');
    }
}
