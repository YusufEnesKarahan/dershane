<?php

namespace App\DTOs\Inventory;

class CreateInventoryItemDTO
{
    public function __construct(
        public int $categoryId,
        public ?int $warehouseId,
        public string $name,
        public string $sku,
        public string $unit,
        public int $quantity,
        public int $minimumQuantity,
        public ?string $description
    ) {}

    public static function fromRequest($request): self
    {
        return new self(
            categoryId: (int) $request->input('category_id'),
            warehouseId: $request->input('warehouse_id') ? (int) $request->input('warehouse_id') : null,
            name: $request->input('name'),
            sku: $request->input('sku'),
            unit: $request->input('unit', 'pcs'),
            quantity: (int) $request->input('quantity', 0),
            minimumQuantity: (int) $request->input('minimum_quantity', 5),
            description: $request->input('description')
        );
    }
}
