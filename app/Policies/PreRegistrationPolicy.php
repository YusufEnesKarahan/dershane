<?php

namespace App\Policies;

use App\Models\PreRegistration;
use App\Models\User;

class PreRegistrationPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('finance.view') || $user->hasPermission('registrations.view') || $user->hasRole('Super Admin');
    }

    public function view(User $user, PreRegistration $preReg): bool
    {
        return $user->hasRole('Super Admin') || $user->branch_id === $preReg->branch_id;
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('finance.create') || $user->hasPermission('registrations.create') || $user->hasRole('Super Admin');
    }

    public function update(User $user, PreRegistration $preReg): bool
    {
        return $user->hasRole('Super Admin') || $user->branch_id === $preReg->branch_id;
    }

    public function delete(User $user, PreRegistration $preReg): bool
    {
        return $user->hasRole('Super Admin') || $user->branch_id === $preReg->branch_id;
    }

    public function convert(User $user, PreRegistration $preReg): bool
    {
        return $user->hasPermission('finance.create') || $user->hasPermission('registrations.create') || $user->hasRole('Super Admin');
    }
}
