<?php

namespace App\Policies;

use App\Models\Exam;
use App\Models\User;

class ExamPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('students.view');
    }

    public function view(User $user, Exam $exam): bool
    {
        return $user->hasPermission('students.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('students.create');
    }

    public function update(User $user, Exam $exam): bool
    {
        return $user->hasPermission('students.create');
    }

    public function delete(User $user, Exam $exam): bool
    {
        return $user->hasPermission('students.create');
    }
}
