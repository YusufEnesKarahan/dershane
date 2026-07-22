<?php

namespace App\Domain\Parent\Actions;

use App\Models\ParentStudent;
use App\Core\Repositories\Interfaces\ParentRepositoryInterface;

class LinkStudent
{
    public function __construct(protected ParentRepositoryInterface $repository) {}

    public function execute(int $parentId, int $studentId, string $relationType): ParentStudent
    {
        return $this->repository->linkStudent($parentId, $studentId, $relationType);
    }
}
