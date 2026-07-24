<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Domain\HR\Services\LeaveService;
use App\Domain\HR\Actions\ApproveLeave;
use App\Domain\HR\Actions\RejectLeave;
use App\DTOs\HR\LeaveRequestDTO;
use App\Models\Employee;
use App\Models\LeaveType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeaveController extends Controller
{
    public function __construct(
        protected LeaveService $leaveService,
        protected ApproveLeave $approveLeaveAction,
        protected RejectLeave $rejectLeaveAction
    ) {}

    public function index()
    {
        $requests = $this->leaveService->allRequests();
        $types = $this->leaveService->allTypes();
        $employees = Employee::where('employment_status', 'Active')->get();
        return view('admin.hr.leaves', compact('requests', 'types', 'employees'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'leave_type_id' => 'required|exists:leave_types,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $dto = LeaveRequestDTO::fromRequest($request);
        $this->leaveService->createRequest($dto);

        return redirect()->route('admin.leaves.index')->with('success', 'İzin talebi başarıyla oluşturuldu.');
    }

    public function approve(int $id)
    {
        $this->approveLeaveAction->execute($id, Auth::id());
        return redirect()->route('admin.leaves.index')->with('success', 'İzin talebi onaylandı.');
    }

    public function reject(int $id)
    {
        $this->rejectLeaveAction->execute($id, Auth::id());
        return redirect()->route('admin.leaves.index')->with('success', 'İzin talebi reddedildi.');
    }
}
