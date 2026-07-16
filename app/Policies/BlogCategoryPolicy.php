<?php
namespace App\Policies;

use App\Models\BlogCategory;
use App\Models\User;
use App\Domain\Auth\Services\AuthorizationService;

class BlogCategoryPolicy
{
    public function __construct(protected AuthorizationService $authService) {}

    public function viewAny(User $user): bool
    {
        return $this->authService->hasPermission($user, 'categories.view');
    }

    public function create(User $user): bool
    {
        return $this->authService->hasPermission($user, 'categories.create');
    }

    public function delete(User $user, BlogCategory $category): bool
    {
        return $this->authService->hasPermission($user, 'categories.delete');
    }
}
