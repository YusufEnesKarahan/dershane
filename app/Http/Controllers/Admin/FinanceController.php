<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentPlan;
use App\Models\Student;
use App\Domain\Finance\Services\FinanceManagementService;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class FinanceController extends Controller
{
    use AuthorizesRequests;

    protected $financeService;

    public function __construct(FinanceManagementService $financeService)
    {
        $this->financeService = $financeService;
    }

    public function index()
    {
        $this->authorize('viewAny', PaymentPlan::class);
        $plans = PaymentPlan::with(['student.user', 'installments'])->orderBy('created_at', 'desc')->get();
        return view('admin.finance.payment-plans', compact('plans'));
    }

    public function create()
    {
        $this->authorize('create', PaymentPlan::class);
        $students = Student::with('user')->get();
        $terms = \App\Models\AcademicTerm::all();
        return view('admin.finance.create-plan', compact('students', 'terms'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', PaymentPlan::class);

        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'academic_term_id' => 'required|exists:academic_terms,id',
            'title' => 'required|string|max:255',
            'total_amount' => 'required|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'installment_count' => 'required|integer|min:1|max:24',
        ]);

        $validated['branch_id'] = auth()->user()->branch_id;

        try {
            $plan = $this->financeService->createPaymentPlan($validated);
            $this->financeService->generateInstallments($plan, $validated['installment_count']);
            return redirect()->route('admin.finance.index')->with('success', 'Ödeme planı ve taksitler oluşturuldu.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function show(PaymentPlan $plan)
    {
        $this->authorize('view', $plan);
        $plan->load(['student.user', 'installments.payments.transactions']);
        return view('admin.finance.show-plan', compact('plan'));
    }

    public function edit(PaymentPlan $plan)
    {
        $this->authorize('update', $plan);
        return view('admin.finance.edit-plan', compact('plan'));
    }

    public function update(Request $request, PaymentPlan $plan)
    {
        $this->authorize('update', $plan);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'total_amount' => 'required|numeric|min:0',
        ]);

        $this->financeService->updatePaymentPlan($plan, $validated);

        return redirect()->route('admin.finance.show', $plan)->with('success', 'Ödeme planı güncellendi.');
    }

    public function applyDiscount(Request $request, PaymentPlan $plan)
    {
        $this->authorize('update', $plan); // Require update permission for discount

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:fixed,percentage',
            'value' => 'required|numeric|min:0',
            'reason' => 'nullable|string'
        ]);

        $validated['branch_id'] = $plan->branch_id;
        $validated['student_id'] = $plan->student_id;
        $validated['approved_by'] = auth()->id();

        if ($validated['type'] === 'percentage') {
            $validated['value'] = ($plan->total_amount * $validated['value']) / 100;
        }

        $this->financeService->applyDiscount($validated, $plan);

        return back()->with('success', 'İndirim başarıyla uygulandı.');
    }
}
