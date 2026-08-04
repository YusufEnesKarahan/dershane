<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Installment;
use Illuminate\Auth\Access\HandlesAuthorization;

class InstallmentPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return $user->hasPermission('finance.view') || $user->hasRole('Student') || $user->hasRole('Parent');
    }

    public function view(User $user, Installment $installment)
    {
        if ($installment->branch_id !== $user->branch_id) {
            return false;
        }

        if ($user->hasRole('Admin') && $user->hasPermission('finance.view')) {
            return true;
        }

        $studentId = $installment->paymentPlan->student_id;

        if ($user->hasRole('Student')) {
            return $studentId === ($user->student->id ?? null);
        }

        if ($user->hasRole('Parent')) {
            $guardian = $user->guardian;
            if (!$guardian) return false;
            return app(\App\Domain\Portal\Services\ParentPortalService::class)->canAccessStudent($guardian->id, $studentId);
        }

        return false;
    }

    public function collect(User $user, Installment $installment)
    {
        if ($installment->branch_id !== $user->branch_id) return false;
        return $user->hasRole('Admin') && $user->hasPermission('finance.collect');
    }
}
