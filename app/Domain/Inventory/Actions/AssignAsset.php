<?php

namespace App\Domain\Inventory\Actions;

use App\Domain\Inventory\Services\AssignmentService;
use App\DTOs\Inventory\AssignAssetDTO;

class AssignAsset
{
    public function __construct(
        protected AssignmentService $assignmentService
    ) {}

    public function execute(AssignAssetDTO $dto)
    {
        return $this->assignmentService->assignAsset($dto);
    }
}
