<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Domain\Finance\Services\FinanceManagementService;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class FinanceDashboardController extends Controller
{
    use AuthorizesRequests;

    public function __construct(protected FinanceManagementService $financeService) {}

    public function index(Request $request)
    {
        $this->authorize('viewAny', Invoice::class);

        $branchId = session('active_branch_id') ?? auth()->user()?->branch_id ?? 1;

        $metrics = $this->financeService->getDashboardMetrics($branchId);
        $branches = Branch::all();

        $recentPayments = Payment::with(['student', 'invoice', 'receiver'])
            ->where('branch_id', $branchId)
            ->latest('payment_date')
            ->take(5)
            ->get();

        $recentInvoices = Invoice::with(['student.classroom', 'guardian'])
            ->where('branch_id', $branchId)
            ->latest('issue_date')
            ->take(5)
            ->get();

        return view('admin.finance.dashboard', compact('metrics', 'branches', 'recentPayments', 'recentInvoices'));
    }
}
