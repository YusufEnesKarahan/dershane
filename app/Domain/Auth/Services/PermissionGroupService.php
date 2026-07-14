<?php
namespace App\Domain\Auth\Services;

class PermissionGroupService
{
    public function getGroupedPermissions(): array
    {
        $config = config('permissions.groups', []);
        $grouped = [];

        foreach ($config as $key => $group) {
            $perms = [];
            foreach ($group['permissions'] as $perm) {
                // Get display meta
                $perms[] = [
                    'name' => $perm,
                    'label' => ucfirst(str_replace('.', ' ', $perm))
                ];
            }
            $grouped[$group['label']] = $perms;
        }

        return $grouped;
    }
}
