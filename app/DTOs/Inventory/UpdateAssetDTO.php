<?php

namespace App\DTOs\Inventory;

class UpdateAssetDTO
{
    public function __construct(
        public int $categoryId,
        public string $name,
        public ?string $brand,
        public ?string $model,
        public ?string $serialNumber,
        public ?string $purchaseDate,
        public float $purchasePrice,
        public ?string $warrantyEndDate,
        public string $status,
        public ?int $locationId,
        public ?string $description
    ) {}

    public static function fromRequest($request): self
    {
        return new self(
            categoryId: (int) $request->input('category_id'),
            name: $request->input('name'),
            brand: $request->input('brand'),
            model: $request->input('model'),
            serialNumber: $request->input('serial_number'),
            purchaseDate: $request->input('purchase_date'),
            purchasePrice: (float) $request->input('purchase_price', 0.0),
            warrantyEndDate: $request->input('warranty_end_date'),
            status: $request->input('status', 'active'),
            locationId: $request->input('location_id') ? (int) $request->input('location_id') : null,
            description: $request->input('description')
        );
    }
}
