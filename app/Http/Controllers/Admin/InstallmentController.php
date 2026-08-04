<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Installment;
use App\Models\PaymentPlan;
use App\Domain\Finance\Services\FinanceManagementService;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

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

        $validated['branch_id'] = $installment->branch_id;
        $validated['student_id'] = $installment->paymentPlan->student_id;
        $validated['payment_date'] = now();
        $validated['received_by'] = auth()->id();
        $validated['installment_id'] = $installment->id;

        $this->financeService->recordPayment($validated, $installment);

        return back()->with('success', 'Tahsilat başarıyla alındı.');
    }
}
