<?php

namespace App\Observers;

use App\Models\Payroll;
use Illuminate\Support\Facades\Log;

class PayrollObserver
{
    public function created(Payroll $payroll): void
    {
        //
    }

    public function updated(Payroll $payroll): void
    {
        //
    }
}
