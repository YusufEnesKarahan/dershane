<?php

namespace App\DTOs\Finance;

class CreateInvoiceDTO
{
    public function __construct(
        public string $invoice_number,
        public int $student_id,
        public string $issue_date,
        public string $due_date,
        public float $total_amount,
        public array $items = [] // [['description' => string, 'quantity' => int, 'unit_price' => float]]
    ) {}
}
