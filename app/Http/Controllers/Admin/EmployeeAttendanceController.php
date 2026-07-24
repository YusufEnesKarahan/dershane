<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Domain\HR\Services\AttendanceService;
use App\Models\Employee;
use Illuminate\Http\Request;

class EmployeeAttendanceController extends Controller
{
    public function __construct(
        protected AttendanceService $attendanceService
    ) {}

    public function index()
    {
        $attendances = $this->attendanceService->allAttendances();
        $employees = Employee::where('employment_status', 'Active')->get();
        return view('admin.hr.attendance', compact('attendances', 'employees'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'date' => 'required|date',
            'check_in' => 'required',
        ]);

        $this->attendanceService->logAttendance(
            (int) $request->employee_id,
            $request->date,
            $request->check_in,
            $request->check_out
        );

        return redirect()->route('admin.attendance.index')->with('success', 'Giriş çıkış kaydı başarıyla eklendi.');
    }
}
