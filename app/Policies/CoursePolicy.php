<?php
namespace App\Policies;

use App\Models\Course;
use App\Models\User;
use App\Domain\Auth\Services\AuthorizationService;

class CoursePolicy
{
    public function __construct(protected AuthorizationService $authService) {}

    public function viewAny(User $user): bool
    {
        return $this->authService->hasPermission($user, 'courses.view');
    }

    public function view(User $user, Course $course): bool
    {
        return $this->authService->hasPermission($user, 'courses.view');
    }

    public function create(User $user): bool
    {
        return $this->authService->hasPermission($user, 'courses.create');
    }

    public function update(User $user, Course $course): bool
    {
        return $this->authService->hasPermission($user, 'courses.update');
    }

    public function delete(User $user, Course $course): bool
    {
        return $this->authService->hasPermission($user, 'courses.delete');
    }
}
