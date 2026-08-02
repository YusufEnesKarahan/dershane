<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentPlan;
use App\Models\Student;
use Illuminate\Http\Request;

class PaymentPlanController extends Controller
{
    public function index()
    {
        $plans = PaymentPlan::with('student')->paginate(15);
        return view('admin.finance.payment-plans.index', compact('plans'));
    }

    public function create()
    {
        $students = Student::all();
        return view('admin.finance.payment-plans.create', compact('students'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'total_installments' => 'required|integer|min:1',
            'installment_amount' => 'required|numeric|min:0.01',
            'start_date' => 'required|date',
        ]);

        PaymentPlan::create($validated);

        return redirect()->route('admin.payment-plans.index')->with('success', 'Ödeme planı oluşturuldu.');
    }

    public function edit(PaymentPlan $paymentPlan)
    {
        $students = Student::all();
        return view('admin.finance.payment-plans.edit', compact('paymentPlan', 'students'));
    }

    public function update(Request $request, PaymentPlan $paymentPlan)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'total_installments' => 'required|integer|min:1',
            'installment_amount' => 'required|numeric|min:0.01',
            'start_date' => 'required|date',
        ]);

        $paymentPlan->update($validated);

        return redirect()->route('admin.payment-plans.index')->with('success', 'Ödeme planı güncellendi.');
    }

    public function destroy(PaymentPlan $paymentPlan)
    {
        $paymentPlan->delete();
        return redirect()->route('admin.payment-plans.index')->with('success', 'Ödeme planı iptal edildi.');
    }
}
