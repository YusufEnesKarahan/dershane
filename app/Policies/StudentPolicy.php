<?php

namespace App\Policies;

use App\Models\Student;
use App\Models\User;

class StudentPolicy
{
    protected function isSameTenant(User $user, Student $student): bool
    {
        if ($user->isAdministrator()) {
            return true;
        }
        return $user->branch_id === $student->branch_id;
    }

    public function viewAny(User $user): bool
    {
        return $user->hasPermission('students.view') || $user->isAdministrator();
    }

    public function view(User $user, Student $student): bool
    {
        return ($user->hasPermission('students.view') || $user->isAdministrator()) && $this->isSameTenant($user, $student);
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('students.create') || $user->isAdministrator();
    }

    public function update(User $user, Student $student): bool
    {
        return ($user->hasPermission('students.update') || $user->isAdministrator()) && $this->isSameTenant($user, $student);
    }

    public function delete(User $user, Student $student): bool
    {
        return ($user->hasPermission('students.delete') || $user->isAdministrator()) && $this->isSameTenant($user, $student);
    }
}
