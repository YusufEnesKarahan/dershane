<?php
namespace App\Core\Repositories\Interfaces;

use App\DTOs\User\UserFilterDTO;
use App\Models\User;

interface UserRepositoryInterface extends BaseRepositoryInterface
{
    public function filterAndPaginate(UserFilterDTO $filters, int $perPage = 15);
    public function findWithTrashed(int $id): ?User;
    public function restore(int $id): bool;
    public function forceDelete(int $id): bool;

    // Bulk Operations
    public function bulkDelete(array $ids): int;
    public function bulkRestore(array $ids): int;
    public function bulkUpdate(array $ids, array $data): int;
    public function bulkStatus(array $ids, string $status): int;
    public function bulkAssignRole(array $ids, int $roleId): void;
    public function bulkAssignBranch(array $ids, int $branchId): void;
}
