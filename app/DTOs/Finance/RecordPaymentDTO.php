<?php

namespace App\DTOs\Finance;

class RecordPaymentDTO
{
    public function __construct(
        public string $payment_number,
        public int $invoice_id,
        public int $student_id,
        public int $payment_method_id,
        public float $amount,
        public string $payment_date,
        public ?string $notes = null
    ) {}
}
