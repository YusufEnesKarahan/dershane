<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Domain\Finance\Services\FinanceManagementService;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class PaymentController extends Controller
{
    use AuthorizesRequests;

    protected $financeService;

    public function __construct(FinanceManagementService $financeService)
    {
        $this->financeService = $financeService;
    }

    public function index()
    {
        $this->authorize('viewAny', Payment::class);
        $payments = Payment::with(['student.user', 'installment.paymentPlan'])->orderBy('created_at', 'desc')->get();
        return view('admin.finance.payments', compact('payments'));
    }

    public function refund(Request $request, Payment $payment)
    {
        $this->authorize('refund', $payment);

        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01|max:'.$payment->amount,
            'reason' => 'required|string'
        ]);

        try {
            $this->financeService->refundPayment($payment, $validated['amount'], $validated['reason'], auth()->id());
            return back()->with('success', 'İade işlemi başarıyla gerçekleştirildi.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
