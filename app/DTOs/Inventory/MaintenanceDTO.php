<?php

namespace App\DTOs\Inventory;

class MaintenanceDTO
{
    public function __construct(
        public int $assetId,
        public int $employeeId,
        public string $maintenanceDate,
        public float $cost,
        public ?string $description,
        public string $status
    ) {}

    public static function fromRequest($request): self
    {
        return new self(
            assetId: (int) $request->input('asset_id'),
            employeeId: (int) $request->input('employee_id'),
            maintenanceDate: $request->input('maintenance_date', now()->toDateString()),
            cost: (float) $request->input('cost', 0.0),
            description: $request->input('description'),
            status: $request->input('status', 'completed')
        );
    }
}
