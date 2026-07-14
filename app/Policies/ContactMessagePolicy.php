<?php
namespace App\Policies;

use App\Models\User;
use App\Models\ContactMessage;
use App\Domain\Auth\Services\AuthorizationService;

class ContactMessagePolicy
{
    public function __construct(protected AuthorizationService $authService) {}

    public function viewAny(User $user): bool
    {
        return $this->authService->hasPermission($user, 'contactmessages.view');
    }

    public function view(User $user, ContactMessage $model): bool
    {
        return $this->authService->hasPermission($user, 'contactmessages.view');
    }

    public function create(User $user): bool
    {
        return $this->authService->hasPermission($user, 'contactmessages.create');
    }

    public function update(User $user, ContactMessage $model): bool
    {
        return $this->authService->hasPermission($user, 'contactmessages.update');
    }

    public function delete(User $user, ContactMessage $model): bool
    {
        return $this->authService->hasPermission($user, 'contactmessages.delete');
    }
}
