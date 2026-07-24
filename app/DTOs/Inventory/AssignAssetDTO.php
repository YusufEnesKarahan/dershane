<?php

namespace App\DTOs\Inventory;

class AssignAssetDTO
{
    public function __construct(
        public int $assetId,
        public int $employeeId,
        public string $assignedDate,
        public ?string $condition,
        public ?string $notes
    ) {}

    public static function fromRequest($request): self
    {
        return new self(
            assetId: (int) $request->input('asset_id'),
            employeeId: (int) $request->input('employee_id'),
            assignedDate: $request->input('assigned_date', now()->toDateString()),
            condition: $request->input('condition'),
            notes: $request->input('notes')
        );
    }
}
