<?php

namespace App\DTOs\Inventory;

class CreatePurchaseOrderDTO
{
    public function __construct(
        public int $supplierId,
        public string $orderNumber,
        public string $orderDate,
        public float $totalAmount,
        public string $status,
        public ?string $notes
    ) {}

    public static function fromRequest($request): self
    {
        return new self(
            supplierId: (int) $request->input('supplier_id'),
            orderNumber: $request->input('order_number'),
            orderDate: $request->input('order_date', now()->toDateString()),
            totalAmount: (float) $request->input('total_amount', 0.0),
            status: $request->input('status', 'pending'),
            notes: $request->input('notes')
        );
    }
}
