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

    public function saved(Employee $employee): void
    {
        \Illuminate\Support\Facades\Cache::forget('hr.analytics.summary');
    }

    public function deleted(Employee $employee): void
    {
        \Illuminate\Support\Facades\Cache::forget('hr.analytics.summary');
    }
}
