<?php

namespace App\Domain\Finance\Actions;

use App\DTOs\Finance\RecordPaymentDTO;
use App\Domain\Finance\Services\PaymentService;
use App\Models\Payment;

class RecordPayment
{
    public function __construct(protected PaymentService $service) {}

    public function execute(RecordPaymentDTO $dto): Payment
    {
        return $this->service->recordPayment($dto);
    }
}
