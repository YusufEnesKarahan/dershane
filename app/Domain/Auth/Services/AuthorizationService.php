<?php
namespace App\Domain\Auth\Services;

use App\Models\User;

class AuthorizationService
{
    public function __construct(protected PermissionCache $permissionCache) {}

    public function hasRole(User $user, string|array $roles): bool
    {
        $userRoles = $user->roles->pluck('name')->toArray();
        $checkRoles = is_array($roles) ? $roles : [$roles];
        
        return count(array_intersect($checkRoles, $userRoles)) > 0;
    }

    public function hasPermission(User $user, string $permission): bool
    {
        if ($this->hasRole($user, 'Administrator')) {
            return true;
        }

        $userPermissions = $this->permissionCache->getUserPermissions($user);
        
        // Handle wildcard permissions like 'users.*'
        foreach ($userPermissions as $userPerm) {
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
