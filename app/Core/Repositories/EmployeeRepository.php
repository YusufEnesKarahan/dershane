<?php

namespace App\Core\Repositories;

use App\Core\Repositories\Interfaces\EmployeeRepositoryInterface;
use App\Models\Employee;

class EmployeeRepository implements EmployeeRepositoryInterface
{
    public function all()
    {
        return Employee::with(['department', 'position', 'user'])->get();
    }

    public function find(int $id)
    {
        return Employee::with(['department', 'position', 'user', 'payrolls', 'leaveRequests', 'attendances', 'expenses', 'advances', 'performanceReviews'])->findOrFail($id);
    }

    public function findByEmployeeNo(string $employeeNo)
    {
        return Employee::with(['department', 'position', 'user'])->where('employee_no', $employeeNo)->firstOrFail();
    }

    public function create(array $data)
    {
        return Employee::create($data);
    }

    public function update(int $id, array $data)
    {
        $employee = Employee::findOrFail($id);
        $employee->update($data);
        return $employee;
    }

    public function delete(int $id)
    {
        $employee = Employee::findOrFail($id);
        return $employee->delete();
    }
}
