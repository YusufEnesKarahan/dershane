<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Domain\Portal\Services\StudentPortalService;
use Illuminate\Http\Request;

class FinancePortalController extends Controller
{
    protected $portalService;

    public function __construct(StudentPortalService $portalService)
    {
        $this->portalService = $portalService;
    }

    public function index()
    {
        $studentId = auth()->user()->student->id;
        
        $summary = $this->portalService->getFinancialSummary($studentId);
        $upcomingInstallments = $this->portalService->getUpcomingInstallments($studentId);
        
        $plans = \App\Models\PaymentPlan::where('student_id', $studentId)
            ->with(['installments.payments'])
            ->orderBy('created_at', 'desc')
            ->get();
            
        $payments = \App\Models\Payment::where('student_id', $studentId)
            ->with('transactions')
            ->orderBy('payment_date', 'desc')
            ->get();

        return view('student.finance.index', compact('summary', 'upcomingInstallments', 'plans', 'payments'));
    }
}
