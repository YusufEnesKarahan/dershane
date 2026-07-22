<?php

namespace App\Core\Repositories\Interfaces;

use App\Models\Payment;
use Illuminate\Support\Collection;

interface PaymentRepositoryInterface
{
    public function getByInvoice(int $invoiceId): Collection;
    public function create(array $data): Payment;
}
