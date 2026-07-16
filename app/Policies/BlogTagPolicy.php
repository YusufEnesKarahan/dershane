<?php
namespace App\Policies;

use App\Models\BlogTag;
use App\Models\User;
use App\Domain\Auth\Services\AuthorizationService;

class BlogTagPolicy
{
    public function __construct(protected AuthorizationService $authService) {}

    public function viewAny(User $user): bool
    {
        return $this->authService->hasPermission($user, 'tags.view');
    }

    public function create(User $user): bool
    {
        return $this->authService->hasPermission($user, 'tags.create');
    }

    public function update(User $user, BlogTag $tag): bool
    {
        return $this->authService->hasPermission($user, 'tags.update');
    }

    public function delete(User $user, BlogTag $tag): bool
    {
        return $this->authService->hasPermission($user, 'tags.delete');
    }
}
