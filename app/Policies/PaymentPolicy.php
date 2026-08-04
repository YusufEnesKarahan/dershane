<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Payment;
use Illuminate\Auth\Access\HandlesAuthorization;

class PaymentPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return $user->hasPermission('finance.view') || $user->hasRole('Student') || $user->hasRole('Parent');
    }

    public function view(User $user, Payment $payment)
    {
        if ($payment->branch_id !== $user->branch_id) {
            return false;
        }

        if ($user->hasRole('Admin') && $user->hasPermission('finance.view')) {
            return true;
        }

        if ($user->hasRole('Student')) {
            return $payment->student_id === ($user->student->id ?? null);
        }

        if ($user->hasRole('Parent')) {
            $guardian = $user->guardian;
            if (!$guardian) return false;
            return app(\App\Domain\Portal\Services\ParentPortalService::class)->canAccessStudent($guardian->id, $payment->student_id);
        }

        return false;
    }

    public function refund(User $user, Payment $payment)
    {
        if ($payment->branch_id !== $user->branch_id) return false;
        return $user->hasRole('Admin') && $user->hasPermission('finance.refund');
    }
}
