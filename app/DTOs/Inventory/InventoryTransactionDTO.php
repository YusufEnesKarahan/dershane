<?php

namespace App\DTOs\Inventory;

class InventoryTransactionDTO
{
    public function __construct(
        public int $itemId,
        public string $type, // purchase, usage, transfer, adjustment
        public int $quantity,
        public ?string $referenceType,
        public ?int $referenceId,
        public ?string $description,
        public ?int $createdBy
    ) {}

    public static function fromRequest($request, ?int $userId = null): self
    {
        return new self(
            itemId: (int) $request->input('item_id'),
            type: $request->input('type'),
            quantity: (int) $request->input('quantity'),
            referenceType: $request->input('reference_type'),
            referenceId: $request->input('reference_id') ? (int) $request->input('reference_id') : null,
            description: $request->input('description'),
            createdBy: $userId ?? ($request->input('created_by') ? (int) $request->input('created_by') : null)
        );
    }
}
