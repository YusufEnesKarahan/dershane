<?php

namespace App\Policies;

use App\Models\PlatformSetting;
use App\Models\User;
use App\Domain\Auth\Services\AuthorizationService;

class PlatformSettingPolicy
{
    public function __construct(protected AuthorizationService $authService) {}

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $this->authService->hasPermission($user, 'settings.view');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user): bool
    {
        return $this->authService->hasPermission($user, 'settings.update');
    }
}
