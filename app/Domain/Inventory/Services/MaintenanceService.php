<?php

namespace App\Domain\Inventory\Services;

use App\Core\Repositories\Interfaces\MaintenanceRepositoryInterface;
use App\Core\Repositories\Interfaces\AssetRepositoryInterface;
use App\DTOs\Inventory\MaintenanceDTO;

class MaintenanceService
{
    public function __construct(
        protected MaintenanceRepositoryInterface $maintenanceRepo,
        protected AssetRepositoryInterface $assetRepo
    ) {}

    public function allRecords()
    {
        return $this->maintenanceRepo->all();
    }

    public function findRecord(int $id)
    {
        return $this->maintenanceRepo->find($id);
    }

    public function createRecord(MaintenanceDTO $dto)
    {
        // Place asset in maintenance status
        $this->assetRepo->update($dto->assetId, ['status' => 'maintenance']);

        return $this->maintenanceRepo->create([
            'asset_id' => $dto->assetId,
            'employee_id' => $dto->employeeId,
            'maintenance_date' => $dto->maintenanceDate,
            'cost' => $dto->cost,
            'description' => $dto->description,
            'status' => $dto->status
        ]);
    }

    public function completeMaintenance(int $id)
    {
        $record = $this->maintenanceRepo->find($id);
        $record->update(['status' => 'completed']);

        // Return asset to active
        $this->assetRepo->update($record->asset_id, ['status' => 'active']);
        return $record;
    }
}
