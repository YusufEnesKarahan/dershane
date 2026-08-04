<?php

namespace App\Policies;

use App\Models\User;
use App\Models\LessonSchedule;
use Illuminate\Auth\Access\HandlesAuthorization;

class SchedulePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasPermission('schedule.view');
    }

    public function view(User $user, LessonSchedule $schedule): bool
    {
        if ($schedule->branch_id !== $user->branch_id) {
            return false;
        }

        if ($user->hasPermission('schedule.view') && ($user->hasRole('Admin') || $user->hasRole('SuperAdmin'))) {
            return true;
        }

        if ($user->hasRole('Teacher')) {
            return $schedule->teacher_id === ($user->teacher->id ?? null);
        }

        if ($user->hasRole('Student')) {
            return $schedule->classroom_id === ($user->student->classroom_id ?? null);
        }

        if ($user->hasRole('Parent')) {
            $student = $user->guardian?->students()->first();
            return $student && $schedule->classroom_id === $student->classroom_id;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('schedule.create');
    }

    public function update(User $user, LessonSchedule $schedule): bool
    {
        if ($schedule->branch_id !== $user->branch_id) {
            return false;
        }

        return $user->hasPermission('schedule.update');
    }

    public function delete(User $user, LessonSchedule $schedule): bool
    {
        if ($schedule->branch_id !== $user->branch_id) {
            return false;
        }

        return $user->hasPermission('schedule.delete');
    }
}
