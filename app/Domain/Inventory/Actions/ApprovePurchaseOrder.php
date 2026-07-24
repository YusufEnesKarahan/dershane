<?php

namespace App\Domain\Inventory\Actions;

use App\Domain\Inventory\Services\PurchaseService;

class ApprovePurchaseOrder
{
    public function __construct(
        protected PurchaseService $purchaseService
    ) {}

    public function execute(int $id)
    {
        return $this->purchaseService->approveOrder($id);
    }
}
