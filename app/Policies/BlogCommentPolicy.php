<?php
namespace App\Policies;

use App\Models\BlogComment;
use App\Models\User;
use App\Domain\Auth\Services\AuthorizationService;

class BlogCommentPolicy
{
    public function __construct(protected AuthorizationService $authService) {}

    public function viewAny(User $user): bool
    {
        return $this->authService->hasPermission($user, 'comments.view');
    }

    public function update(User $user, BlogComment $comment): bool
    {
        return $this->authService->hasPermission($user, 'moderation.approve');
    }

    public function delete(User $user, BlogComment $comment): bool
    {
        return $this->authService->hasPermission($user, 'comments.delete');
    }
}
