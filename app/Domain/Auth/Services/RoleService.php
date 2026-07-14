<?php
namespace App\Domain\Auth\Services;

use App\Core\Repositories\Interfaces\RoleRepositoryInterface;
use App\DTOs\Role\RoleFilterDTO;

class RoleService
{
    public function __construct(protected RoleRepositoryInterface $repository) {}

    public function paginate(RoleFilterDTO $filters, int $perPage = 15)
    {
        return $this->repository->filterAndPaginate($filters, $perPage);
    }
}
