<?php
namespace App\Domain\Auth\Actions\Role;

use App\Core\Repositories\Interfaces\RoleRepositoryInterface;
use App\Domain\Auth\Services\SystemRoleGuard;
use App\Models\Role;

class DeleteRoleAction
{
    public function __construct(
        protected RoleRepositoryInterface $repository,
        protected SystemRoleGuard $guard
    ) {}

    public function execute(Role $role, bool $force = false): bool
    {
        $this->guard->checkDeletion($role);

        if ($force) {
            return $this->repository->forceDelete($role->id);
        }

        return $this->repository->delete($role->id);
    }
}
