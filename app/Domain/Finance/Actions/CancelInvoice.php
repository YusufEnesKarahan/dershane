<?php

namespace App\Domain\Finance\Actions;

use App\Models\Invoice;
use App\Models\StudentDebt;

class CancelInvoice
{
    public function execute(Invoice $invoice): void
    {
        $invoice->update(['status' => 'Cancelled']);

        $debt = StudentDebt::where('invoice_id', $invoice->id)->first();
        if ($debt) {
            $debt->delete();
        }
    }
}
