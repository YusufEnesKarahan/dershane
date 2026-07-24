<?php

namespace App\Policies;

use App\Models\Teacher;
use App\Models\User;

class TeacherPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('teachers.view');
    }

    public function view(User $user, Teacher $teacher): bool
    {
        return $user->hasPermission('teachers.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('teachers.create');
    }

    public function update(User $user, Teacher $teacher): bool
    {
        return $user->hasPermission('teachers.update');
    }

    public function delete(User $user, Teacher $teacher): bool
    {
        return $user->hasPermission('teachers.delete');
    }
}
