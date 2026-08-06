<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Installment;
use App\Models\Payment;
use App\Domain\Finance\Services\FinanceManagementService;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Str;

class InstallmentController extends Controller
{
    use AuthorizesRequests;

    protected $financeService;

    public function __construct(FinanceManagementService $financeService)
    {
        $this->financeService = $financeService;
    }

    public function index()
    {
        $this->authorize('viewAny', Installment::class);
        $installments = Installment::with(['paymentPlan.student.user'])->orderBy('due_date', 'asc')->get();
        return view('admin.finance.installments', compact('installments'));
    }

    public function collect(Request $request, Installment $installment)
    {
        $this->authorize('collect', $installment);

        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01|max:'.$installment->remaining_amount,
            'payment_method' => 'required|string',
            'reference_no' => 'nullable|string',
            'notes' => 'nullable|string'
        ]);

        $amount = (float) $validated['amount'];
        $installment->paid_amount = (float) ($installment->paid_amount ?? 0) + $amount;
        $installment->remaining_amount = max(0, (float) $installment->amount - (float) $installment->paid_amount);
        $installment->status = $installment->remaining_amount <= 0 ? 'paid' : 'partial';
        $installment->save();

        Payment::create([
            'branch_id' => $installment->branch_id,
            'payment_number' => 'PAY-' . date('Ymd') . '-' . Str::upper(Str::random(4)),
            'installment_id' => $installment->id,
            'student_id' => $installment->paymentPlan?->student_id,
            'amount' => $amount,
            'payment_method' => $validated['payment_method'],
            'payment_date' => now(),
            'reference_no' => $validated['reference_no'] ?? null,
            'received_by' => auth()->id() ?? 1,
            'notes' => $validated['notes'] ?? null,
            'status' => 'Completed',
        ]);

        return back()->with('success', 'Tahsilat başarıyla alındı.');
    }
}
