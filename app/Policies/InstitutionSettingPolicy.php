<?php

namespace App\Policies;

use App\Models\User;
use App\Domain\Auth\Dictionaries\PermissionDictionary;

class InstitutionSettingPolicy
{
    /**
     * Determine whether the user can view institution settings.
     */
    public function view(User $user): bool
    {
        if ($user->hasRole('Teacher') || $user->hasRole('Student') || $user->hasRole('Parent')) {
            return false;
        }

        return $user->hasRole('Super Admin') || 
               $user->hasRole('Branch Admin') || 
               $user->hasPermission(PermissionDictionary::INSTITUTION_SETTINGS_VIEW);
    }

    /**
     * Determine whether the user can update institution settings.
     */
    public function update(User $user): bool
    {
        if ($user->hasRole('Teacher') || $user->hasRole('Student') || $user->hasRole('Parent')) {
            return false;
        }

        return $user->hasRole('Super Admin') || 
               $user->hasRole('Branch Admin') || 
               $user->hasPermission(PermissionDictionary::INSTITUTION_SETTINGS_UPDATE);
    }
}
