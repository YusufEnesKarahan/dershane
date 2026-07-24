<?php

namespace App\Domain\Inventory\Actions;

use App\Domain\Inventory\Services\StockMovementService;
use App\DTOs\Inventory\InventoryTransactionDTO;

class CreateInventoryTransaction
{
    public function __construct(
        protected StockMovementService $stockMovementService
    ) {}

    public function execute(InventoryTransactionDTO $dto)
    {
        return $this->stockMovementService->createTransaction($dto);
    }
}
