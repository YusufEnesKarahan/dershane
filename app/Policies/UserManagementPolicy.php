<?php

namespace App\Policies;

use App\Models\User;
use App\Domain\Auth\Dictionaries\PermissionDictionary;

class UserManagementPolicy
{
    /**
     * Determine whether the user can view users list.
     */
    public function viewAny(User $user): bool
    {
        if ($user->hasRole('Teacher') || $user->hasRole('Student') || $user->hasRole('Parent')) {
            return false;
        }

        return $user->hasRole('Super Admin') || 
               $user->hasRole('Branch Admin') || 
               $user->hasPermission(PermissionDictionary::USER_VIEW) || 
               $user->hasPermission(PermissionDictionary::USERS_VIEW);
    }

    /**
     * Determine whether the user can view a specific user.
     */
    public function view(User $user, User $model): bool
    {
        if ($user->hasRole('Teacher') || $user->hasRole('Student') || $user->hasRole('Parent')) {
            return false;
        }

        if ($user->hasRole('Super Admin')) {
            return true;
        }

        if ($user->hasRole('Branch Admin') && $user->branch_id === $model->branch_id) {
            return true;
        }

        return ($user->hasPermission(PermissionDictionary::USER_VIEW) || $user->hasPermission(PermissionDictionary::USERS_VIEW)) &&
               ($user->branch_id === null || $user->branch_id === $model->branch_id);
    }

    /**
     * Determine whether the user can create users.
     */
    public function create(User $user): bool
    {
        if ($user->hasRole('Teacher') || $user->hasRole('Student') || $user->hasRole('Parent')) {
            return false;
        }

        return $user->hasRole('Super Admin') || 
               $user->hasRole('Branch Admin') || 
               $user->hasPermission(PermissionDictionary::USER_CREATE) || 
               $user->hasPermission(PermissionDictionary::USERS_CREATE);
    }

    /**
     * Determine whether the user can update a specific user.
     */
    public function update(User $user, User $model): bool
    {
        if ($user->hasRole('Teacher') || $user->hasRole('Student') || $user->hasRole('Parent')) {
            return false;
        }

        if ($user->hasRole('Super Admin')) {
            return true;
        }

        if ($user->hasRole('Branch Admin') && $user->branch_id === $model->branch_id) {
            return true;
        }

        return ($user->hasPermission(PermissionDictionary::USER_UPDATE) || $user->hasPermission(PermissionDictionary::USERS_UPDATE)) &&
               ($user->branch_id === null || $user->branch_id === $model->branch_id);
    }

    /**
     * Determine whether the user can delete a specific user.
     */
    public function delete(User $user, User $model): bool
    {
        if ($user->id === $model->id) {
            return false; // Users cannot delete themselves
        }

        if ($user->hasRole('Teacher') || $user->hasRole('Student') || $user->hasRole('Parent')) {
            return false;
        }

        if ($user->hasRole('Super Admin')) {
            return true;
        }

        if ($user->hasRole('Branch Admin') && $user->branch_id === $model->branch_id) {
            return true;
        }

        return ($user->hasPermission(PermissionDictionary::USER_DELETE) || $user->hasPermission(PermissionDictionary::USERS_DELETE)) &&
               ($user->branch_id === null || $user->branch_id === $model->branch_id);
    }
}
