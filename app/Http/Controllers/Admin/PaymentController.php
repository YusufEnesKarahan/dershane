<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\DTOs\Finance\RecordPaymentDTO;
use App\Domain\Finance\Actions\RecordPayment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function store(Request $request, RecordPayment $action)
    {
        $request->validate([
            'invoice_id' => 'required|exists:invoices,id',
            'payment_method_id' => 'required|exists:payment_methods,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_number' => 'required|string|unique:payments,payment_number',
        ]);

        $invoice = Invoice::findOrFail($request->invoice_id);

        $dto = new RecordPaymentDTO(
            $request->payment_number,
            $invoice->id,
            $invoice->student_id,
            (int) $request->payment_method_id,
            (float) $request->amount,
            now()->format('Y-m-d H:i:s'),
            $request->notes
        );

        $action->execute($dto);

        return redirect()->back()->with('success', 'Tahsilat kaydı başarıyla işlendi ve fatura durumu güncellendi.');
    }
}
