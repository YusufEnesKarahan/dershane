<?php
namespace App\Domain\Auth\Actions\Role;

use App\DTOs\Role\CreateRoleDTO;
use App\Core\Repositories\Interfaces\RoleRepositoryInterface;
use App\Models\Role;

class CreateRoleAction
{
    public function __construct(protected RoleRepositoryInterface $repository) {}

    public function execute(CreateRoleDTO $dto): Role
    {
        $role = $this->repository->create([
            'name' => $dto->name,
            'description' => $dto->description,
            'color' => $dto->color,
        ]);

        if (!empty($dto->permissions)) {
            $role->permissions()->sync($dto->permissions);
        }

        return $role;
    }
}
