<?php
namespace App\Domain\Auth\Services;

use App\Models\Role;

class PermissionMatrixService
{
    public function __construct(protected PermissionGroupService $groupService) {}

    public function getMatrix(Role $role): array
    {
        $groups = $this->groupService->getGroupedPermissions();
        $rolePerms = $role->permissions->pluck('name')->toArray();
        
        $matrix = [];
        foreach ($groups as $groupLabel => $perms) {
            $matrix[$groupLabel] = array_map(function($perm) use ($rolePerms) {
                return [
                    'name' => $perm['name'],
                    'label' => $perm['label'],
                    'checked' => in_array($perm['name'], $rolePerms, true),
                ];
            }, $perms);
        }

        return $matrix;
    }
}
