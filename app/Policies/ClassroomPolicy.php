<?php

namespace App\Policies;

use App\Models\Classroom;
use App\Models\User;

class ClassroomPolicy
{
    protected function isSameTenant(User $user, Classroom $classroom): bool
    {
        if ($user->isAdministrator()) {
            return true;
        }
        return $user->branch_id === $classroom->branch_id;
    }

    public function viewAny(User $user): bool
    {
        return $user->hasPermission('classrooms.view') || $user->hasPermission('classrooms.manage') || $user->isAdministrator();
    }

    public function view(User $user, Classroom $classroom): bool
    {
        return ($user->hasPermission('classrooms.view') || $user->hasPermission('classrooms.manage') || $user->isAdministrator()) && $this->isSameTenant($user, $classroom);
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('classrooms.manage') || $user->isAdministrator();
    }

    public function update(User $user, Classroom $classroom): bool
    {
        return ($user->hasPermission('classrooms.manage') || $user->isAdministrator()) && $this->isSameTenant($user, $classroom);
    }

    public function delete(User $user, Classroom $classroom): bool
    {
        return ($user->hasPermission('classrooms.manage') || $user->isAdministrator()) && $this->isSameTenant($user, $classroom);
    }
}
