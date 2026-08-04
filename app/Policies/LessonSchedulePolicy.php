<?php

namespace App\Policies;

use App\Models\User;
use App\Models\LessonSchedule;
use App\Domain\Auth\Dictionaries\PermissionDictionary;

class LessonSchedulePolicy
{
    public function viewAny(User $user)
    {
        return $user->hasPermission(PermissionDictionary::SCHEDULES_VIEW);
    }

    public function view(User $user, LessonSchedule $schedule)
    {
        if ($user->branch_id !== $schedule->branch_id) {
            return false;
        }

        if ($user->hasRole('Teacher')) {
            // A teacher can view schedules assigned to them
            return $schedule->teacher_id === $user->teacher->id || 
                   $schedule->additionalTeachers()->where('teachers.id', $user->teacher->id)->exists();
        }

        return $user->hasPermission(PermissionDictionary::SCHEDULES_VIEW);
    }

    public function create(User $user)
    {
        return $user->hasPermission(PermissionDictionary::SCHEDULES_CREATE);
    }

    public function update(User $user, LessonSchedule $schedule)
    {
        return $user->hasPermission(PermissionDictionary::SCHEDULES_UPDATE) &&
               $user->branch_id === $schedule->branch_id;
    }

    public function delete(User $user, LessonSchedule $schedule)
    {
        return $user->hasPermission(PermissionDictionary::SCHEDULES_DELETE) &&
               $user->branch_id === $schedule->branch_id;
    }

    public function manage(User $user)
    {
        return $user->hasPermission(PermissionDictionary::SCHEDULES_MANAGE);
    }
}
