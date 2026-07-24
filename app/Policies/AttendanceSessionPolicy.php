<?php

namespace App\Policies;

use App\Models\AttendanceSession;
use App\Models\User;

class AttendanceSessionPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('attendance.view');
    }

    public function view(User $user, AttendanceSession $session): bool
    {
        return $user->hasPermission('attendance.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('attendance.manage');
    }

    public function update(User $user, AttendanceSession $session): bool
    {
        return $user->hasPermission('attendance.manage');
    }

    public function delete(User $user, AttendanceSession $session): bool
    {
        return $user->hasPermission('attendance.manage');
    }
}
