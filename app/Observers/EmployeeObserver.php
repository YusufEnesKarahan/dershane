<?php

namespace App\Observers;

use App\Models\Employee;
use Illuminate\Support\Facades\Log;

class EmployeeObserver
{
    public function creating(Employee $employee): void
    {
        if (empty($employee->employee_no)) {
            $employee->employee_no = 'EMP-' . date('Ymd') . '-' . rand(1000, 9999);
        }
    }

    public function created(Employee $employee): void
    {
        Log::info("Employee created: {$employee->employee_no}");
    }

    public function updated(Employee $employee): void
    {
        Log::info("Employee updated: {$employee->employee_no}");
    }
}
