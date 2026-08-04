<?php

namespace App\Policies;

use App\Models\AttendanceSession;
use App\Models\User;
use App\Domain\Auth\Dictionaries\PermissionDictionary;

class AttendanceSessionPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission(PermissionDictionary::ATTENDANCE_VIEW);
    }

    public function view(User $user, AttendanceSession $session): bool
    {
        if ($user->hasPermission(PermissionDictionary::ATTENDANCE_VIEW)) {
            if ($user->hasRole('Teacher')) {
                return $user->branch_id === $session->branch_id && $user->teacher->id === $session->teacher_id;
            }
            return $user->branch_id === $session->branch_id;
        }
        return false;
    }

    public function create(User $user): bool
    {
        return $user->hasPermission(PermissionDictionary::ATTENDANCE_CREATE);
    }

    public function update(User $user, AttendanceSession $session): bool
    {
        if ($user->hasPermission(PermissionDictionary::ATTENDANCE_UPDATE)) {
            if ($user->hasRole('Teacher')) {
                return $user->branch_id === $session->branch_id && $user->teacher->id === $session->teacher_id;
            }
            return $user->branch_id === $session->branch_id;
        }
        return false;
    }

    public function delete(User $user, AttendanceSession $session): bool
    {
        return $user->hasPermission(PermissionDictionary::ATTENDANCE_DELETE) && $user->branch_id === $session->branch_id;
    }
}
