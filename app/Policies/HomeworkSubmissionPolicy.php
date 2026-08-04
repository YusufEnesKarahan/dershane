<?php

namespace App\Policies;

use App\Models\User;
use App\Models\HomeworkSubmission;
use Illuminate\Auth\Access\HandlesAuthorization;

class HomeworkSubmissionPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return $user->hasPermission('homework.view');
    }

    public function view(User $user, HomeworkSubmission $submission)
    {
        if ($submission->branch_id !== $user->branch_id) {
            return false;
        }

        if ($user->hasRole('Admin') && $user->hasPermission('homework.view')) {
            return true;
        }

        if ($user->hasRole('Teacher')) {
            return $submission->homework->teacher_id === ($user->teacher->id ?? null);
        }

        if ($user->hasRole('Student')) {
            return $submission->student_id === ($user->student->id ?? null);
        }

        return false;
    }

    public function grade(User $user, HomeworkSubmission $submission)
    {
        if ($submission->branch_id !== $user->branch_id) {
            return false;
        }

        if ($user->hasRole('Admin') && $user->hasPermission('homework.grade')) {
            return true;
        }

        if ($user->hasRole('Teacher')) {
            return $user->hasPermission('homework.grade') && $submission->homework->teacher_id === ($user->teacher->id ?? null);
        }

        return false;
    }
}
