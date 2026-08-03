<?php

namespace App\Policies;

use App\Models\Teacher;
use App\Models\User;

class TeacherPolicy
{
    protected function isSameTenant(User $user, Teacher $teacher): bool
    {
        if ($user->isAdministrator()) {
            return true;
        }
        return $user->branch_id === $teacher->branch_id;
    }

    public function viewAny(User $user): bool
    {
        return $user->hasPermission('teachers.view') || $user->isAdministrator();
    }

    public function view(User $user, Teacher $teacher): bool
    {
        if ($user->id === $teacher->user_id) {
            return true;
        }

        return ($user->hasPermission('teachers.view') || $user->isAdministrator()) && $this->isSameTenant($user, $teacher);
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('teachers.create') || $user->isAdministrator();
    }

    public function update(User $user, Teacher $teacher): bool
    {
        return ($user->hasPermission('teachers.update') || $user->isAdministrator()) && $this->isSameTenant($user, $teacher);
    }

    public function delete(User $user, Teacher $teacher): bool
    {
        return ($user->hasPermission('teachers.delete') || $user->isAdministrator()) && $this->isSameTenant($user, $teacher);
    }
}
