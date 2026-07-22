<?php

namespace App\Domain\Finance\Services;

use App\DTOs\Finance\RecordPaymentDTO;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\StudentDebt;
use App\Core\Repositories\Interfaces\PaymentRepositoryInterface;

class PaymentService
{
    public function __construct(protected PaymentRepositoryInterface $repository) {}

    public function recordPayment(RecordPaymentDTO $dto): Payment
    {
        $payment = $this->repository->create([
            'payment_number' => $dto->payment_number,
            'invoice_id' => $dto->invoice_id,
            'student_id' => $dto->student_id,
            'payment_method_id' => $dto->payment_method_id,
            'amount' => $dto->amount,
            'payment_date' => $dto->payment_date,
            'notes' => $dto->notes,
            'status' => 'Completed',
        ]);

        if ($dto->invoice_id) {
            $invoice = Invoice::findOrFail($dto->invoice_id);
            $newPaid = $invoice->paid_amount + $dto->amount;
            $status = $newPaid >= $invoice->total_amount ? 'Paid' : 'Partial';

            $invoice->update([
                'paid_amount' => $newPaid,
                'status' => $status,
            ]);

            // Update student debt
            $debt = StudentDebt::where('invoice_id', $invoice->id)->first();
            if ($debt) {
                $rem = max(0, $debt->amount - $newPaid);
                $debtStatus = $rem == 0 ? 'Paid' : 'Partial';
                $debt->update([
                    'remaining_amount' => $rem,
                    'status' => $debtStatus,
                ]);
            }
        }

        return $payment;
    }
}
