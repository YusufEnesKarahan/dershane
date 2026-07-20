<?php
namespace App\Policies;

use App\Models\AttendanceSession;
use App\Models\User;

class AttendanceSessionPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, AttendanceSession $session): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, AttendanceSession $session): bool
    {
        return true;
    }

    public function delete(User $user, AttendanceSession $session): bool
    {
        return true;
    }
}
