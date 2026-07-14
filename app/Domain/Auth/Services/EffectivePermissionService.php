<?php
namespace App\Domain\Auth\Services;

use App\Models\User;

class EffectivePermissionService
{
    public function __construct(protected PermissionCache $permissionCache) {}

    public function effectivePermissions(User $user): array
    {
        // 1. Base user role permissions from cache
        $permissions = $this->permissionCache->getUserPermissions($user);

        // 2. Filter out by Edition limits (if any)
        // Stubs for future integration with Tenant/Company Edition limits
        
        return $permissions;
    }
}
