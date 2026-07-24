<?php

namespace App\Domain\Inventory\Actions;

use App\Domain\Inventory\Services\MaintenanceService;
use App\DTOs\Inventory\MaintenanceDTO;

class CreateMaintenanceRecord
{
    public function __construct(
        protected MaintenanceService $maintenanceService
    ) {}

    public function execute(MaintenanceDTO $dto)
    {
        return $this->maintenanceService->createRecord($dto);
    }
}
