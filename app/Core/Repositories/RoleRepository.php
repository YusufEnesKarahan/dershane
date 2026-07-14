<?php
namespace App\Core\Repositories;

use App\Core\Repositories\Interfaces\RoleRepositoryInterface;
use App\DTOs\Role\RoleFilterDTO;
use App\Models\Role;

class RoleRepository extends BaseRepository implements RoleRepositoryInterface
{
    public function __construct(Role $model)
    {
        parent::__construct($model);
    }

    public function filterAndPaginate(RoleFilterDTO $filters, int $perPage = 15)
    {
        $query = $this->model->newQuery()->withCount('users', 'permissions');

        if ($filters->search) {
            $query->where('name', 'like', "%{$filters->search}%")
                  ->orWhere('description', 'like', "%{$filters->search}%");
        }

        return $query->paginate($perPage);
    }

    public function findWithTrashed(int $id): ?Role
    {
        return $this->model->withTrashed()->find($id);
    }

    public function restore(int $id): bool
    {
        $record = $this->findWithTrashed($id);
        return $record ? $record->restore() : false;
    }

    public function forceDelete(int $id): bool
    {
        $record = $this->findWithTrashed($id);
        return $record ? $record->forceDelete() : false;
    }
}
