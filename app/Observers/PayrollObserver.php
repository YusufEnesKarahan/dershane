<?php

namespace App\Observers;

use App\Models\Payroll;
use Illuminate\Support\Facades\Log;

class PayrollObserver
{
    public function saved(Payroll $payroll): void
    {
        \Illuminate\Support\Facades\Cache::forget('hr.analytics.summary');
    }

    public function deleted(Payroll $payroll): void
    {
        \Illuminate\Support\Facades\Cache::forget('hr.analytics.summary');
    }
}
