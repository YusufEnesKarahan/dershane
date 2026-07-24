<?php

namespace App\Observers;

use App\Models\Payroll;
use Illuminate\Support\Facades\Log;

class PayrollObserver
{
    public function created(Payroll $payroll): void
    {
        Log::info("Payroll generated for employee ID: {$payroll->employee_id} for period: {$payroll->month}/{$payroll->year}");
    }

    public function updated(Payroll $payroll): void
    {
        Log::info("Payroll updated for employee ID: {$payroll->employee_id}. Status: {$payroll->status}");
    }
}
