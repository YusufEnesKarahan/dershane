<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Domain\HR\Services\HRService;
use App\Domain\HR\Actions\CreateEmployee;
use App\Domain\HR\Actions\UpdateEmployee;
use App\Domain\HR\Actions\TerminateEmployee;
use App\DTOs\HR\CreateEmployeeDTO;
use App\DTOs\HR\UpdateEmployeeDTO;
use App\Models\Department;
use App\Models\Position;
use App\Models\User;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function __construct(
        protected HRService $hrService,
        protected CreateEmployee $createEmployeeAction,
        protected UpdateEmployee $updateEmployeeAction,
        protected TerminateEmployee $terminateAction
    ) {}

    public function index()
    {
        $employees = $this->hrService->allEmployees();
        $departments = Department::all();
        $positions = Position::all();
        $users = User::all();
        return view('admin.hr.employees', compact('employees', 'departments', 'positions', 'users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'hire_date' => 'required|date',
            'salary' => 'required|numeric',
        ]);

        $dto = CreateEmployeeDTO::fromRequest($request);
        $this->createEmployeeAction->execute($dto);

        return redirect()->route('admin.employees.index')->with('success', 'Personel başarıyla eklendi.');
    }

    public function update(Request $request, int $id)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'salary' => 'required|numeric',
        ]);

        $dto = UpdateEmployeeDTO::fromRequest($request);
        $this->updateEmployeeAction->execute($id, $dto);

        return redirect()->route('admin.employees.index')->with('success', 'Personel başarıyla güncellendi.');
    }

    public function destroy(int $id)
    {
        $this->terminateAction->execute($id);
        return redirect()->route('admin.employees.index')->with('success', 'Personel sözleşmesi feshedildi.');
    }
}
