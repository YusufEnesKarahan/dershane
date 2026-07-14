<?php
namespace App\Domain\Auth\Services;

use App\Models\Role;

class RoleCloneService
{
    public function clone(Role $role, string $newName): Role
    {
        $newRole = Role::create([
            'name' => $newName,
            'guard_name' => $role->guard_name ?? 'web'
        ]);

        $permissions = $role->permissions->pluck('id')->toArray();
        $newRole->permissions()->sync($permissions);

        return $newRole;
    }
}
