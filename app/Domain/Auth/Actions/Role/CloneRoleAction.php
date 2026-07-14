<?php
namespace App\Domain\Auth\Actions\Role;

use App\Models\Role;
use App\Domain\Auth\Services\RoleCloneService;

class CloneRoleAction
{
    public function __construct(protected RoleCloneService $cloneService) {}

    public function execute(Role $role, string $newName, ?string $description = null, ?string $color = null): Role
    {
        $cloned = $this->cloneService->clone($role, $newName, ['permissions' => true]);
        
        $cloned->update([
            'description' => $description ?? $role->description,
            'color' => $color ?? $role->color,
        ]);

        return $cloned;
    }
}
