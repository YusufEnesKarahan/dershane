<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Domain\HR\Services\AdvanceService;
use App\Domain\HR\Actions\ApproveAdvance;
use App\DTOs\HR\AdvanceDTO;
use App\Models\Employee;
use Illuminate\Http\Request;

class AdvanceController extends Controller
{
    public function __construct(
        protected AdvanceService $advanceService,
        protected ApproveAdvance $approveAction
    ) {}

    public function index()
    {
        $advances = $this->advanceService->allAdvances();
        $employees = Employee::where('employment_status', 'Active')->get();
        return view('admin.hr.advances', compact('advances', 'employees'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'amount' => 'required|numeric',
        ]);

        $dto = AdvanceDTO::fromRequest($request);
        $this->advanceService->createAdvance($dto);

        return redirect()->route('admin.advances.index')->with('success', 'Avans talebi oluşturuldu.');
    }

    public function approve(int $id)
    {
        $this->approveAction->execute($id);
        return redirect()->route('admin.advances.index')->with('success', 'Avans talebi onaylandı.');
    }

    public function reject(int $id)
    {
        $this->advanceService->rejectAdvance($id);
        return redirect()->route('admin.advances.index')->with('success', 'Avans talebi reddedildi.');
    }
}
