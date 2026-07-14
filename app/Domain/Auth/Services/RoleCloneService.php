<?php
namespace App\Domain\Auth\Services;

use App\Models\Role;

class RoleCloneService
{
    public function clone(Role $role, string $newName, array $options = []): Role
    {
        $newRole = Role::create([
            'name' => $newName,
            'guard_name' => $role->guard_name ?? 'web'
        ]);

        if ($options['permissions'] ?? true) {
            $permissions = $role->permissions->pluck('id')->toArray();
            $newRole->permissions()->sync($permissions);
        }

        // Additional hooks for future features (e.g. sidebar preferences, widget default clones)
        
        return $newRole;
    }
}
