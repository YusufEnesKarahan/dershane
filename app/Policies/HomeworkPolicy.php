<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Homework;
use Illuminate\Auth\Access\HandlesAuthorization;

class HomeworkPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return $user->hasPermission('homework.view');
    }

    public function view(User $user, Homework $homework)
    {
        if ($user->hasPermission('homework.view') && $user->hasRole('Admin')) {
            return $homework->branch_id === $user->branch_id;
        }

        if ($user->hasRole('Teacher')) {
            return $homework->teacher_id === ($user->teacher->id ?? null);
        }

        if ($user->hasRole('Student')) {
            return $homework->classroom_id === ($user->student->classroom_id ?? null) && $homework->status !== 'draft';
        }

        return false;
    }

    public function create(User $user)
    {
        return $user->hasPermission('homework.create');
    }

    public function update(User $user, Homework $homework)
    {
        if ($homework->branch_id !== $user->branch_id) {
            return false;
        }

        if ($user->hasRole('Admin')) {
            return $user->hasPermission('homework.update');
        }

        if ($user->hasRole('Teacher')) {
            return $user->hasPermission('homework.update') && $homework->teacher_id === ($user->teacher->id ?? null);
        }

        return false;
    }

    public function delete(User $user, Homework $homework)
    {
        if ($homework->branch_id !== $user->branch_id) {
            return false;
        }

        if ($user->hasRole('Admin')) {
            return $user->hasPermission('homework.delete');
        }

        if ($user->hasRole('Teacher')) {
            return $user->hasPermission('homework.delete') && $homework->teacher_id === ($user->teacher->id ?? null);
        }

        return false;
    }

    public function submit(User $user, Homework $homework)
    {
        if ($user->hasRole('Student')) {
            return $homework->classroom_id === ($user->student->classroom_id ?? null) && $homework->status === 'published';
        }
        return false;
    }

    public function report(User $user, Homework $homework = null)
    {
        if ($user->hasRole('Admin') && $user->hasPermission('homework.report')) {
            return true;
        }

        if ($user->hasRole('Teacher')) {
            return $user->hasPermission('homework.report');
        }

        return false;
    }
}
