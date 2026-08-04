<?php

namespace App\Policies;

use App\Models\StudentGuidanceRecord;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class StudentGuidanceRecordPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasPermission('guidance.view') || $user->hasRole('Teacher');
    }

    public function view(User $user, StudentGuidanceRecord $record): bool
    {
        if ($user->hasPermission('guidance.view')) {
            return $user->branch_id === $record->branch_id;
        }

        if ($user->hasRole('Teacher')) {
            // Simplified: in real world we check if teacher is assigned to student's class
            return $user->branch_id === $record->branch_id;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('guidance.create') || $user->hasRole('Teacher');
    }

    public function update(User $user, StudentGuidanceRecord $record): bool
    {
        if ($user->hasPermission('guidance.update')) {
            return $user->branch_id === $record->branch_id;
        }

        if ($user->hasRole('Teacher')) {
            return $user->branch_id === $record->branch_id && $record->teacher_id === $user->teacher->id;
        }

        return false;
    }

    public function delete(User $user, StudentGuidanceRecord $record): bool
    {
        if ($user->hasPermission('guidance.delete')) {
            return $user->branch_id === $record->branch_id;
        }

        if ($user->hasRole('Teacher')) {
            return $user->branch_id === $record->branch_id && $record->teacher_id === $user->teacher->id;
        }

        return false;
    }
}
