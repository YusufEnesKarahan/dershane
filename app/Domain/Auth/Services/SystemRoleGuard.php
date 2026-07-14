<?php
namespace App\Domain\Auth\Services;

use App\Models\Role;

class SystemRoleGuard
{
    protected const PROTECTED_ROLES = ['Administrator', 'Super Admin'];

    public function isProtected(string|Role $role): bool
    {
        $name = $role instanceof Role ? $role->name : $role;
        return in_array($name, self::PROTECTED_ROLES, true);
    }

    public function checkDeletion(Role $role): void
    {
        if ($this->isProtected($role)) {
            abort(403, 'System role cannot be deleted.');
        }
    }

    public function checkRename(Role $role, string $newName): void
    {
        if ($this->isProtected($role) && $role->name !== $newName) {
            abort(403, 'System role name cannot be modified.');
        }
    }
}
