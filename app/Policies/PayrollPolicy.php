<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Payroll;

class PayrollPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Payroll $payroll): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Payroll $payroll): bool
    {
        return true;
    }

    public function delete(User $user, Payroll $payroll): bool
    {
        return true;
    }
}
