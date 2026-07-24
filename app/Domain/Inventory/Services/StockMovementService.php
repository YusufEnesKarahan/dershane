<?php

namespace App\Domain\Inventory\Services;

use App\Core\Repositories\Interfaces\InventoryRepositoryInterface;
use App\DTOs\Inventory\InventoryTransactionDTO;

class StockMovementService
{
    public function __construct(
        protected InventoryRepositoryInterface $inventoryRepo
    ) {}

    public function createTransaction(InventoryTransactionDTO $dto)
    {
        $item = $this->inventoryRepo->find($dto->itemId);

        // Adjust quantity based on type
        // purchase, usage, transfer, adjustment
        $newQty = $item->quantity;
        if ($dto->type === 'purchase' || $dto->type === 'transfer' && $dto->quantity > 0) {
            $newQty += $dto->quantity;
        } elseif ($dto->type === 'usage' || $dto->type === 'transfer' && $dto->quantity < 0) {
            $newQty -= abs($dto->quantity);
        } elseif ($dto->type === 'adjustment') {
            $newQty = $dto->quantity; // direct set
        }

        $this->inventoryRepo->update($dto->itemId, ['quantity' => $newQty]);

        return $this->inventoryRepo->createTransaction([
            'item_id' => $dto->itemId,
            'type' => $dto->type,
            'quantity' => $dto->quantity,
            'reference_type' => $dto->referenceType,
            'reference_id' => $dto->referenceId,
            'description' => $dto->description,
            'created_by' => $dto->createdBy
        ]);
    }

    public function allTransactions(?int $itemId = null)
    {
        return $this->inventoryRepo->getTransactions($itemId);
    }
}
