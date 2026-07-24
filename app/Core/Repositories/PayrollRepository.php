<?php

namespace App\Core\Repositories;

use App\Core\Repositories\Interfaces\PayrollRepositoryInterface;
use App\Models\Payroll;

class PayrollRepository implements PayrollRepositoryInterface
{
    public function all()
    {
        return Payroll::with('employee.position')->get();
    }

    public function find(int $id)
    {
        return Payroll::with('employee')->findOrFail($id);
    }

    public function create(array $data)
    {
        return Payroll::create($data);
    }

    public function update(int $id, array $data)
    {
        $payroll = Payroll::findOrFail($id);
        $payroll->update($data);
        return $payroll;
    }

    public function delete(int $id)
    {
        $payroll = Payroll::findOrFail($id);
        return $payroll->delete();
    }

    public function getForEmployee(int $employeeId)
    {
        return Payroll::where('employee_id', $employeeId)->orderBy('year', 'desc')->orderBy('month', 'desc')->get();
    }
}
