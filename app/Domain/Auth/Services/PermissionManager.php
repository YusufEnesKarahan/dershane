<?php
namespace App\Domain\Auth\Services;

use App\Models\Permission;

class PermissionManager
{
    public function createPermission(string $name): Permission
    {
        return Permission::firstOrCreate(['name' => $name]);
    }
}
