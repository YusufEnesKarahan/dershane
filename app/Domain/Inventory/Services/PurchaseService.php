<?php

namespace App\Domain\Inventory\Services;

use App\Core\Repositories\Interfaces\InventoryRepositoryInterface;
use App\DTOs\Inventory\CreatePurchaseOrderDTO;

class PurchaseService
{
    public function __construct(
        protected InventoryRepositoryInterface $inventoryRepo
    ) {}

    public function allOrders()
    {
        return $this->inventoryRepo->allPurchaseOrders();
    }

    public function findOrder(int $id)
    {
        return $this->inventoryRepo->findPurchaseOrder($id);
    }

    public function createOrder(CreatePurchaseOrderDTO $dto)
    {
        return $this->inventoryRepo->createPurchaseOrder([
            'supplier_id' => $dto->supplierId,
            'order_number' => $dto->orderNumber,
            'order_date' => $dto->orderDate,
            'total_amount' => $dto->totalAmount,
            'status' => $dto->status,
            'notes' => $dto->notes
        ]);
    }

    public function approveOrder(int $id)
    {
        return $this->inventoryRepo->updatePurchaseOrder($id, ['status' => 'approved']);
    }

    public function completeOrder(int $id)
    {
        return $this->inventoryRepo->updatePurchaseOrder($id, ['status' => 'completed']);
    }
}
