<?php
namespace App\Domain\Auth\Services;

use App\Models\Role;

class RoleManager
{
    public function __construct(protected PermissionCache $cache) {}

    public function assignPermissionToRole(Role $role, string|array $permissions): void
    {
        $role->permissions()->syncWithoutDetaching($permissions);
        $this->cache->clearRoleCache($role);
    }

    public function revokePermissionFromRole(Role $role, string|array $permissions): void
    {
        $role->permissions()->detach($permissions);
        $this->cache->clearRoleCache($role);
    }
}
