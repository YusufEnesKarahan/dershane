<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Domain\HR\Services\PayrollService;
use App\Domain\HR\Actions\GeneratePayroll;
use App\DTOs\HR\CreatePayrollDTO;
use App\Models\Employee;
use App\Models\Payroll;
use Illuminate\Http\Request;

class PayrollController extends Controller
{
    public function __construct(
        protected PayrollService $payrollService,
        protected GeneratePayroll $generatePayrollAction
    ) {}

    public function index()
    {
        $payrolls = $this->payrollService->allPayrolls();
        $employees = Employee::where('employment_status', 'Active')->get();
        return view('admin.hr.payroll', compact('payrolls', 'employees'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'month' => 'required|integer|between:1,12',
            'year' => 'required|integer',
        ]);

        $dto = CreatePayrollDTO::fromRequest($request);
        $this->generatePayrollAction->execute($dto);

        return redirect()->route('admin.payroll.index')->with('success', 'Bordro başarıyla oluşturuldu.');
    }

    public function approve(int $id)
    {
        $this->payrollService->approvePayroll($id);
        return redirect()->route('admin.payroll.index')->with('success', 'Bordro onaylandı.');
    }

    public function pay(int $id)
    {
        $this->payrollService->payPayroll($id);
        return redirect()->route('admin.payroll.index')->with('success', 'Bordro ödendi olarak işaretlendi.');
    }
}
