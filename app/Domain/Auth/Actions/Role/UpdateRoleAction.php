<?php
namespace App\Domain\Auth\Actions\Role;

use App\DTOs\Role\UpdateRoleDTO;
use App\Core\Repositories\Interfaces\RoleRepositoryInterface;
use App\Domain\Auth\Services\SystemRoleGuard;
use App\Domain\Auth\Services\PermissionCache;
use App\Models\Role;

class UpdateRoleAction
{
    public function __construct(
        protected RoleRepositoryInterface $repository,
        protected SystemRoleGuard $guard,
        protected PermissionCache $cache
    ) {}

    public function execute(Role $role, UpdateRoleDTO $dto): Role
    {
        $this->guard->checkRename($role, $dto->name);

        if ($this->guard->isProtected($role) && empty($dto->permissions)) {
            abort(403, 'System role permissions cannot be entirely removed.');
        }

        $role->update([
            'name' => $dto->name,
            'description' => $dto->description,
            'color' => $dto->color,
        ]);

        $role->permissions()->sync($dto->permissions);

        // Clear and rebuild cache for all affected users
        $this->cache->clearRoleCache($role);

        return $role;
    }
}
