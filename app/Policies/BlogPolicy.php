<?php
namespace App\Policies;

use App\Models\Blog;
use App\Models\User;
use App\Domain\Auth\Services\AuthorizationService;

class BlogPolicy
{
    public function __construct(protected AuthorizationService $authService) {}

    public function viewAny(User $user): bool
    {
        return $this->authService->hasPermission($user, 'blogs.view');
    }

    public function view(User $user, Blog $blog): bool
    {
        return $this->authService->hasPermission($user, 'blogs.view');
    }

    public function create(User $user): bool
    {
        return $this->authService->hasPermission($user, 'blogs.create');
    }

    public function update(User $user, Blog $blog): bool
    {
        return $this->authService->hasPermission($user, 'blogs.update');
    }

    public function delete(User $user, Blog $blog): bool
    {
        return $this->authService->hasPermission($user, 'blogs.delete');
    }
}
