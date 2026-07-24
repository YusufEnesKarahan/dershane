<?php

namespace App\Domain\Inventory\Actions;

use App\Domain\Inventory\Services\AssignmentService;

class ReturnAsset
{
    public function __construct(
        protected AssignmentService $assignmentService
    ) {}

    public function execute(int $assignmentId, ?string $returnedDate = null, ?string $condition = null, ?string $notes = null)
    {
        return $this->assignmentService->returnAsset($assignmentId, $returnedDate, $condition, $notes);
    }
}
