<?php

namespace App\Policies;

use App\Models\User;
use App\Models\LeaveRequest;

class LeavePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('hr.view');
    }

    public function view(User $user, LeaveRequest $request): bool
    {
        return $user->hasPermission('hr.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('hr.view');
    }

    public function update(User $user, LeaveRequest $request): bool
    {
        return $user->hasPermission('hr.view');
    }

    public function delete(User $user, LeaveRequest $request): bool
    {
        return $user->hasPermission('hr.update');
    }

    public function approve(User $user, LeaveRequest $request): bool
    {
        return $user->hasPermission('hr.approve');
    }
}
