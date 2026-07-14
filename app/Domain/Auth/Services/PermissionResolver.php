<?php
namespace App\Domain\Auth\Services;

use App\Models\User;

class PermissionResolver
{
    public function __construct(
        protected EffectivePermissionService $effectivePermissionService
    ) {}

    public function hasPermission(User $user, string $permission): bool
    {
        if ($user->hasRole('Administrator')) {
            return true;
        }

        $effective = $this->effectivePermissionService->effectivePermissions($user);

        foreach ($effective as $userPerm) {
            if ($userPerm === $permission) {
                return true;
            }
            if (str_ends_with($userPerm, '.*')) {
                $prefix = substr($userPerm, 0, -1);
                if (str_starts_with($permission, $prefix)) {
                    return true;
                }
            }
        }

        return false;
    }
}
