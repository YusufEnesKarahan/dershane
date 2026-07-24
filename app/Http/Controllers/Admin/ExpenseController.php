<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Domain\HR\Services\ExpenseService;
use App\Domain\HR\Actions\ApproveExpense;
use App\DTOs\HR\ExpenseDTO;
use App\Models\Employee;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function __construct(
        protected ExpenseService $expenseService,
        protected ApproveExpense $approveAction
    ) {}

    public function index()
    {
        $expenses = $this->expenseService->allExpenses();
        $employees = Employee::where('employment_status', 'Active')->get();
        return view('admin.hr.expenses', compact('expenses', 'employees'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric',
            'category' => 'required|string',
        ]);

        $dto = ExpenseDTO::fromRequest($request);
        $this->expenseService->createExpense($dto);

        return redirect()->route('admin.expenses.index')->with('success', 'Masraf kaydı başarıyla oluşturuldu.');
    }

    public function approve(int $id)
    {
        $this->approveAction->execute($id);
        return redirect()->route('admin.expenses.index')->with('success', 'Masraf kaydı onaylandı.');
    }

    public function reject(int $id)
    {
        $this->expenseService->rejectExpense($id);
        return redirect()->route('admin.expenses.index')->with('success', 'Masraf kaydı reddedildi.');
    }
}
