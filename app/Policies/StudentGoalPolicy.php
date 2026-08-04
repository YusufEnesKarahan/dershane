<?php

namespace App\Policies;

use App\Models\StudentGoal;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class StudentGoalPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasPermission('guidance.goal') || $user->hasRole('Teacher') || $user->hasRole('Student');
    }

    public function view(User $user, StudentGoal $goal): bool
    {
        if ($user->hasPermission('guidance.goal')) {
            return $user->branch_id === $goal->branch_id;
        }

        if ($user->hasRole('Teacher')) {
            return $user->branch_id === $goal->branch_id;
        }

        if ($user->hasRole('Student')) {
            return $user->student && $user->student->id === $goal->student_id;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('guidance.goal') || $user->hasRole('Teacher');
    }

    public function update(User $user, StudentGoal $goal): bool
    {
        if ($user->hasPermission('guidance.goal')) {
            return $user->branch_id === $goal->branch_id;
        }

        if ($user->hasRole('Teacher')) {
            return $user->branch_id === $goal->branch_id;
        }

        return false;
    }

    public function delete(User $user, StudentGoal $goal): bool
    {
        return $this->update($user, $goal);
    }
}
