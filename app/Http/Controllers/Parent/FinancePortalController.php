<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Domain\Portal\Services\ParentPortalService;
use Illuminate\Http\Request;

class FinancePortalController extends Controller
{
    protected $portalService;

    public function __construct(ParentPortalService $portalService)
    {
        $this->portalService = $portalService;
    }

    public function index()
    {
        $guardian = auth()->user()?->guardian ?? \App\Models\StudentGuardian::first();

        $summaries = [];
        $installments = [];
        $plans = [];
        
        $students = $guardian ? $this->portalService->getChildren($guardian->id) : collect();
        if ($students->isEmpty()) {
            $students = \App\Models\Student::take(1)->get();
        }
        
        foreach ($students as $student) {
            $summaries[$student->id] = $this->portalService->getChildFinancialSummary($student->id);
            $installments[$student->id] = $this->portalService->getChildUpcomingInstallments($student->id);
            
            $plans[$student->id] = \App\Models\PaymentPlan::where('student_id', $student->id)
                ->with(['installments.payments'])
                ->orderBy('created_at', 'desc')
                ->get();
        }

        return view('parent.finance.index', compact('students', 'summaries', 'installments', 'plans'));
    }
}
