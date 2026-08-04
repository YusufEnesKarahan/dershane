<?php

namespace App\Policies;

use App\Models\User;
use App\Models\PaymentPlan;
use Illuminate\Auth\Access\HandlesAuthorization;

class PaymentPlanPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return $user->hasPermission('finance.view') || $user->hasRole('Student') || $user->hasRole('Parent');
    }

    public function view(User $user, PaymentPlan $plan)
    {
        if ($plan->branch_id !== $user->branch_id) {
            return false;
        }

        if ($user->hasRole('Admin') && $user->hasPermission('finance.view')) {
            return true;
        }

        if ($user->hasRole('Student')) {
            return $plan->student_id === ($user->student->id ?? null);
        }

        if ($user->hasRole('Parent')) {
            $guardian = $user->guardian;
            if (!$guardian) return false;
            return app(\App\Domain\Portal\Services\ParentPortalService::class)->canAccessStudent($guardian->id, $plan->student_id);
        }

        return false;
    }

    public function create(User $user)
    {
        return $user->hasRole('Admin') && $user->hasPermission('finance.create');
    }

    public function update(User $user, PaymentPlan $plan)
    {
        if ($plan->branch_id !== $user->branch_id) return false;
        return $user->hasRole('Admin') && $user->hasPermission('finance.update');
    }

    public function delete(User $user, PaymentPlan $plan)
    {
        if ($plan->branch_id !== $user->branch_id) return false;
        return $user->hasRole('Admin') && $user->hasPermission('finance.delete');
    }
}
