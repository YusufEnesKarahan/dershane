<?php
namespace App\Core\Repositories\Interfaces;

use App\DTOs\Role\RoleFilterDTO;
use App\Models\Role;

interface RoleRepositoryInterface extends BaseRepositoryInterface
{
    public function filterAndPaginate(RoleFilterDTO $filters, int $perPage = 15);
    public function findWithTrashed(int $id): ?Role;
    public function restore(int $id): bool;
    public function forceDelete(int $id): bool;
}
