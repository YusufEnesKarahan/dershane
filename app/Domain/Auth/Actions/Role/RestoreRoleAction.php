<?php
namespace App\Domain\Auth\Actions\Role;

use App\Core\Repositories\Interfaces\RoleRepositoryInterface;

class RestoreRoleAction
{
    public function __construct(protected RoleRepositoryInterface $repository) {}

    public function execute(int $id): bool
    {
        return $this->repository->restore($id);
    }
}
