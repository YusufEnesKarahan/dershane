<?php

namespace App\Domain\Finance\Actions;

use App\DTOs\Finance\CreateInvoiceDTO;
use App\Domain\Finance\Services\BillingService;
use App\Models\Invoice;

class CreateInvoice
{
    public function __construct(protected BillingService $service) {}

    public function execute(CreateInvoiceDTO $dto): Invoice
    {
        return $this->service->createInvoice($dto);
    }
}
