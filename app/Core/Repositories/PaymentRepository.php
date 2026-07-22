<?php

namespace App\Core\Repositories;

use App\Models\Payment;
use App\Core\Repositories\Interfaces\PaymentRepositoryInterface;
use Illuminate\Support\Collection;

class PaymentRepository implements PaymentRepositoryInterface
{
    public function getByInvoice(int $invoiceId): Collection
    {
        return Payment::with(['paymentMethod', 'student'])
            ->where('invoice_id', $invoiceId)
            ->orderBy('payment_date', 'desc')
            ->get();
    }

    public function create(array $data): Payment
    {
        return Payment::create($data);
    }
}
