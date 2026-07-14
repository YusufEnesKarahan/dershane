<?php
namespace App\Domain\CMS\Actions;

use App\Core\Repositories\Interfaces\PageRepositoryInterface;

class RestorePageAction
{
    public function __construct(protected PageRepositoryInterface $repository) {}

    public function execute(int $id): bool
    {
        return $this->repository->restore($id);
    }
}
