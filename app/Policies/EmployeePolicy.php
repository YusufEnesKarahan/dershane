<?php

namespace App\Policies;

use App\Models\Employee;
use App\Models\User;

class EmployeePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('hr.view');
    }

    public function view(User $user, Employee $employee): bool
    {
        return $user->hasPermission('hr.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('hr.create');
    }

    public function update(User $user, Employee $employee): bool
    {
        return $user->hasPermission('hr.update');
    }

    public function delete(User $user, Employee $employee): bool
    {
        return $user->hasPermission('hr.update');
    }
}
