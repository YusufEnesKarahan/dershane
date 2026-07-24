<?php

namespace App\Policies;

use App\Models\User;
use App\Models\LeaveRequest;

class LeavePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, LeaveRequest $request): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, LeaveRequest $request): bool
    {
        return true;
    }

    public function delete(User $user, LeaveRequest $request): bool
    {
        return true;
    }
}
