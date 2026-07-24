<?php

namespace App\Domain\HR\Services;

use App\Core\Repositories\Interfaces\PayrollRepositoryInterface;
use App\DTOs\HR\CreatePayrollDTO;
use App\Models\Employee;

class PayrollService
{
    public function __construct(
        protected PayrollRepositoryInterface $payrollRepo
    ) {}

    public function allPayrolls()
    {
        return $this->payrollRepo->all();
    }

    public function findPayroll(int $id)
    {
        return $this->payrollRepo->find($id);
    }

    public function generatePayroll(CreatePayrollDTO $dto)
    {
        $employee = Employee::findOrFail($dto->employeeId);
        $gross = $employee->salary + $dto->bonus + $dto->overtimeAmount;
        $insurance = round($gross * 0.14, 2);
        $tax = round(($gross - $insurance) * 0.15, 2);
        $net = $gross - $insurance - $tax - $dto->deductions;

        return $this->payrollRepo->create([
            'employee_id' => $dto->employeeId,
            'month' => $dto->month,
            'year' => $dto->year,
            'gross_salary' => $gross,
            'bonus' => $dto->bonus,
            'overtime_amount' => $dto->overtimeAmount,
            'deductions' => $dto->deductions,
            'tax' => $tax,
            'insurance' => $insurance,
            'net_salary' => $net,
            'status' => 'Draft'
        ]);
    }

    public function payPayroll(int $id)
    {
        return $this->payrollRepo->update($id, [
            'status' => 'Paid',
            'payment_date' => now()->toDateString()
        ]);
    }

    public function approvePayroll(int $id)
    {
        return $this->payrollRepo->update($id, [
            'status' => 'Approved'
        ]);
    }
}
