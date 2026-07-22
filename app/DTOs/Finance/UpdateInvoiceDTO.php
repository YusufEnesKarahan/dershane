<?php

namespace App\DTOs\Finance;

class UpdateInvoiceDTO
{
    public function __construct(
        public int $invoice_id,
        public string $due_date,
        public string $status
    ) {}
}
