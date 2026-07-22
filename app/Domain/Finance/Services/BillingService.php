<?php

namespace App\Domain\Finance\Services;

use App\DTOs\Finance\CreateInvoiceDTO;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\StudentDebt;
use App\Core\Repositories\Interfaces\InvoiceRepositoryInterface;

class BillingService
{
    public function __construct(protected InvoiceRepositoryInterface $repository) {}

    public function createInvoice(CreateInvoiceDTO $dto): Invoice
    {
        $invoice = $this->repository->create([
            'invoice_number' => $dto->invoice_number,
            'student_id' => $dto->student_id,
            'issue_date' => $dto->issue_date,
            'due_date' => $dto->due_date,
            'total_amount' => $dto->total_amount,
            'paid_amount' => 0.00,
            'status' => 'Pending',
        ]);

        if (!empty($dto->items)) {
            foreach ($dto->items as $item) {
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'description' => $item['description'],
                    'quantity' => $item['quantity'] ?? 1,
                    'unit_price' => $item['unit_price'],
                    'total_price' => ($item['quantity'] ?? 1) * $item['unit_price'],
                ]);
            }
        }

        // Auto-create Student Debt record
        StudentDebt::create([
            'student_id' => $dto->student_id,
            'invoice_id' => $invoice->id,
            'amount' => $dto->total_amount,
            'remaining_amount' => $dto->total_amount,
            'due_date' => $dto->due_date,
            'status' => 'Unpaid',
        ]);

        return $invoice;
    }
}
