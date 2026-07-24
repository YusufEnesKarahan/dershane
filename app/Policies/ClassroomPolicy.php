<?php

namespace App\Policies;

use App\Models\Classroom;
use App\Models\User;

class ClassroomPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('classrooms.view');
    }

    public function view(User $user, Classroom $classroom): bool
    {
        return $user->hasPermission('classrooms.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('classrooms.manage');
    }

    public function update(User $user, Classroom $classroom): bool
    {
        return $user->hasPermission('classrooms.manage');
    }

    public function delete(User $user, Classroom $classroom): bool
    {
        return $user->hasPermission('classrooms.manage');
    }
}
