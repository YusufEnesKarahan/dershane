<?php
namespace App\Domain\Auth\Actions\User;

use App\Core\Repositories\Interfaces\UserRepositoryInterface;
use App\Models\User;

class RestoreUserAction
{
    public function __construct(protected UserRepositoryInterface $repository) {}

    public function execute(int $id): bool
    {
        return $this->repository->restore($id);
    }
}
