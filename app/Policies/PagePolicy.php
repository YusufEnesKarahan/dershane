<?php
namespace App\Policies;

use App\Models\User;
use App\Models\Page;
use App\Domain\Auth\Services\AuthorizationService;

class PagePolicy
{
    public function __construct(protected AuthorizationService $authService) {}

    public function viewAny(User $user): bool
    {
        return $this->authService->hasPermission($user, 'pages.view');
    }

    public function view(User $user, Page $model): bool
    {
        return $this->authService->hasPermission($user, 'pages.view');
    }

    public function create(User $user): bool
    {
        return $this->authService->hasPermission($user, 'pages.create');
    }

    public function update(User $user, Page $model): bool
    {
        return $this->authService->hasPermission($user, 'pages.update');
    }

    public function delete(User $user, Page $model): bool
    {
        return $this->authService->hasPermission($user, 'pages.delete');
    }
}
