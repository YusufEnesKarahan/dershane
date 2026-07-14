<?php
namespace App\Policies;

use App\Models\User;
use App\Models\Lead;
use App\Domain\Auth\Services\AuthorizationService;

class LeadPolicy
{
    public function __construct(protected AuthorizationService $authService) {}

    public function viewAny(User $user): bool
    {
        return $this->authService->hasPermission($user, 'leads.view');
    }

    public function view(User $user, Lead $model): bool
    {
        return $this->authService->hasPermission($user, 'leads.view');
    }

    public function create(User $user): bool
    {
        return $this->authService->hasPermission($user, 'leads.create');
    }

    public function update(User $user, Lead $model): bool
    {
        return $this->authService->hasPermission($user, 'leads.update');
    }

    public function delete(User $user, Lead $model): bool
    {
        return $this->authService->hasPermission($user, 'leads.delete');
    }
}
