<?php

namespace App\Policies;

use App\Models\StudentMeeting;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class StudentMeetingPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasPermission('guidance.meeting') || $user->hasRole('Teacher') || $user->hasRole('Student') || $user->hasRole('Parent');
    }

    public function view(User $user, StudentMeeting $meeting): bool
    {
        if ($user->hasPermission('guidance.meeting')) {
            return $user->branch_id === $meeting->branch_id;
        }

        if ($user->hasRole('Teacher')) {
            return $user->branch_id === $meeting->branch_id;
        }

        if ($user->hasRole('Student')) {
            return $user->student && $user->student->id === $meeting->student_id;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('guidance.meeting') || $user->hasRole('Teacher');
    }

    public function update(User $user, StudentMeeting $meeting): bool
    {
        if ($user->hasPermission('guidance.meeting')) {
            return $user->branch_id === $meeting->branch_id;
        }

        if ($user->hasRole('Teacher')) {
            return $user->branch_id === $meeting->branch_id && $meeting->teacher_id === $user->teacher->id;
        }

        return false;
    }

    public function delete(User $user, StudentMeeting $meeting): bool
    {
        return $this->update($user, $meeting);
    }
}
