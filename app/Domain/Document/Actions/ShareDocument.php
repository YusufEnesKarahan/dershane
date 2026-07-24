<?php

namespace App\Domain\Document\Actions;

use App\Domain\Document\Services\DocumentPermissionService;
use App\DTOs\Document\DocumentPermissionDTO;

class ShareDocument
{
    public function __construct(
        protected DocumentPermissionService $permissionService
    ) {}

    public function execute(DocumentPermissionDTO $dto)
    {
        return $this->permissionService->setPermission($dto);
    }
}
