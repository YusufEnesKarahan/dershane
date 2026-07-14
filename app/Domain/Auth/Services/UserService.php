<?php
namespace App\Domain\Auth\Services;

use App\Core\Repositories\Interfaces\UserRepositoryInterface;
use App\DTOs\User\UserFilterDTO;

class UserService
{
    public function __construct(protected UserRepositoryInterface $repository) {}

    public function paginate(UserFilterDTO $filters, int $perPage = 15)
    {
        return $this->repository->filterAndPaginate($filters, $perPage);
    }
}
