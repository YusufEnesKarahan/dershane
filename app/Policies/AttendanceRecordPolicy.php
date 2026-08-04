<?php

namespace App\Policies;

use App\Models\AttendanceRecord;
use App\Models\User;
use App\Domain\Auth\Dictionaries\PermissionDictionary;

class AttendanceRecordPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission(PermissionDictionary::ATTENDANCE_VIEW);
    }

    public function view(User $user, AttendanceRecord $record): bool
    {
        if ($user->hasPermission(PermissionDictionary::ATTENDANCE_VIEW) && $user->branch_id === $record->branch_id) {
            if ($user->hasRole('Teacher')) {
                return $user->teacher && $user->teacher->id === $record->teacher_id;
            }
            if ($user->hasRole('Student')) {
                return $user->student && $user->student->id === $record->student_id;
            }
            if ($user->hasRole('Parent')) {
                return $user->guardian && $user->guardian->students()->where('students.id', $record->student_id)->exists();
            }
            return true;
        }
        return false;
    }

    public function create(User $user): bool
    {
        return $user->hasPermission(PermissionDictionary::ATTENDANCE_CREATE);
    }

    public function update(User $user, AttendanceRecord $record): bool
    {
        if ($user->hasPermission(PermissionDictionary::ATTENDANCE_UPDATE) && $user->branch_id === $record->branch_id) {
            if ($user->hasRole('Teacher')) {
                return $user->teacher && $user->teacher->id === $record->teacher_id;
            }
            return true;
        }
        return false;
    }

    public function delete(User $user, AttendanceRecord $record): bool
    {
        return $user->hasPermission(PermissionDictionary::ATTENDANCE_DELETE) && $user->branch_id === $record->branch_id;
    }
}
