<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Refund;
use App\Models\Payment;
use Illuminate\Http\Request;

class RefundController extends Controller
{
    public function index()
    {
        $refunds = Refund::with('payment.student')->paginate(15);
        return view('admin.finance.refunds.index', compact('refunds'));
    }

    public function create()
    {
        $payments = Payment::with('student')->get();
        return view('admin.finance.refunds.create', compact('payments'));
    }

    public function store(Request $request)
    {
        $payment = Payment::findOrFail($request->payment_id);

        $validated = $request->validate([
            'payment_id' => 'required|exists:payments,id',
            'amount' => 'required|numeric|min:0.01',
            'reason' => 'required|string|max:1000',
            'refund_date' => 'required|date',
        ]);
        $validated['branch_id'] = $payment->branch_id ?? auth()->user()?->branch_id;

        Refund::create($validated);

        return redirect()->route('admin.refunds.index')->with('success', 'İade talebi oluşturuldu.');
    }

    public function edit(Refund $refund)
    {
        $payments = Payment::with('student')->get();
        return view('admin.finance.refunds.edit', compact('refund', 'payments'));
    }

    public function update(Request $request, Refund $refund)
    {
        Payment::findOrFail($request->payment_id);

        $validated = $request->validate([
            'payment_id' => 'required|exists:payments,id',
            'amount' => 'required|numeric|min:0.01',
            'reason' => 'required|string|max:1000',
            'refund_date' => 'required|date',
        ]);

        $refund->update($validated);

        return redirect()->route('admin.refunds.index')->with('success', 'İade talebi güncellendi.');
    }

    public function destroy(Refund $refund)
    {
        $refund->delete();
        return redirect()->route('admin.refunds.index')->with('success', 'İade iptal edildi.');
    }
}
