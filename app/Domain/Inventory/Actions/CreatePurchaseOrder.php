<?php

namespace App\Domain\Inventory\Actions;

use App\Domain\Inventory\Services\PurchaseService;
use App\DTOs\Inventory\CreatePurchaseOrderDTO;

class CreatePurchaseOrder
{
    public function __construct(
        protected PurchaseService $purchaseService
    ) {}

    public function execute(CreatePurchaseOrderDTO $dto)
    {
        return $this->purchaseService->createOrder($dto);
    }
}
