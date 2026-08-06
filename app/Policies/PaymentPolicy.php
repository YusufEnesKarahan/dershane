<?php

namespace App\Policies;

use App\Models\Payment;
use App\Models\User;

class PaymentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('finance.view') || $user->hasRole('Super Admin');
    }

    public function view(User $user, Payment $payment): bool
    {
        return $user->hasRole('Super Admin') || $user->branch_id === $payment->branch_id;
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('finance.collect') || $user->hasPermission('finance.create') || $user->hasRole('Super Admin');
    }

    public function delete(User $user, Payment $payment): bool
    {
        return $user->hasPermission('finance.delete') || $user->hasRole('Super Admin');
    }
}
