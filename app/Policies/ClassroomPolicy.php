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
        return $user->hasPermission('classes.view') || $user->isAdministrator();
    }

    public function view(User $user, Classroom $classroom): bool
    {
        return ($user->hasPermission('classes.view') || $user->isAdministrator()) && $this->isSameTenant($user, $classroom);
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('classes.create') || $user->isAdministrator();
    }

    public function update(User $user, Classroom $classroom): bool
    {
        return ($user->hasPermission('classes.update') || $user->isAdministrator()) && $this->isSameTenant($user, $classroom);
    }

    public function delete(User $user, Classroom $classroom): bool
    {
        return ($user->hasPermission('classes.delete') || $user->isAdministrator()) && $this->isSameTenant($user, $classroom);
    }
}
