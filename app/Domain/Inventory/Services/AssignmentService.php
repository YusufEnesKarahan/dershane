<?php

namespace App\Domain\Inventory\Services;

use App\Core\Repositories\Interfaces\AssetRepositoryInterface;
use App\DTOs\Inventory\AssignAssetDTO;

class AssignmentService
{
    public function __construct(
        protected AssetRepositoryInterface $assetRepo
    ) {}

    public function allAssignments()
    {
        return $this->assetRepo->allAssignments();
    }

    public function assignAsset(AssignAssetDTO $dto)
    {
        // Set asset status to maintenance/assigned if active
        // But the assign action marks status in assignment log.
        return $this->assetRepo->createAssignment([
            'asset_id' => $dto->assetId,
            'employee_id' => $dto->employeeId,
            'assigned_date' => $dto->assignedDate,
            'condition' => $dto->condition,
            'notes' => $dto->notes,
            'status' => 'assigned'
        ]);
    }

    public function returnAsset(int $assignmentId, ?string $returnedDate = null, ?string $condition = null, ?string $notes = null)
    {
        $assignment = $this->assetRepo->findAssignment($assignmentId);
        return $this->assetRepo->updateAssignment($assignmentId, [
            'returned_date' => $returnedDate ?? now()->toDateString(),
            'condition' => $condition ?? $assignment->condition,
            'notes' => $notes ?? $assignment->notes,
            'status' => 'returned'
        ]);
    }
}
