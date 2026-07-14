<?php
namespace App\Core\Repositories;

use App\Core\Repositories\Interfaces\UserRepositoryInterface;
use App\DTOs\User\UserFilterDTO;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    public function filterAndPaginate(UserFilterDTO $filters, int $perPage = 15)
    {
        $query = $this->model->newQuery()->with('roles', 'branch');

        if ($filters->search) {
            $query->where(function($q) use ($filters) {
                $q->where('name', 'like', "%{$filters->search}%")
                  ->orWhere('email', 'like', "%{$filters->search}%");
            });
        }

        if ($filters->status) {
            $query->where('status', $filters->status);
        }

        if ($filters->branch_id) {
            $query->where('branch_id', $filters->branch_id);
        }

        if ($filters->role) {
            $query->whereHas('roles', function($q) use ($filters) {
                $q->where('name', $filters->role);
            });
        }

        return $query->paginate($perPage);
    }

    public function findWithTrashed(int $id): ?User
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

    public function bulkAssignRole(array $ids, int $roleId): void
    {
        DB::transaction(function () use ($ids, $roleId) {
            foreach ($ids as $userId) {
                $user = $this->find($userId);
                $user->roles()->sync([$roleId]);
            }
        });
    }

    public function bulkAssignBranch(array $ids, int $branchId): void
    {
        $this->model->whereIn('id', $ids)->update(['branch_id' => $branchId]);
    }
}
