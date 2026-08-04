<?php

namespace App\Policies;

use App\Models\User;
use App\Domain\Auth\Dictionaries\PermissionDictionary;

class OnboardingPolicy
{
    /**
     * Determine whether the user can view onboarding wizard.
     */
    public function view(User $user): bool
    {
        if ($user->hasRole('Teacher') || $user->hasRole('Student') || $user->hasRole('Parent')) {
            return false;
        }

        return $user->hasRole('Super Admin') || $user->hasRole('Branch Admin') || $user->hasPermission(PermissionDictionary::ONBOARDING_VIEW);
    }

    /**
     * Determine whether the user can manage/complete onboarding wizard steps.
     */
    public function manage(User $user): bool
    {
        if ($user->hasRole('Teacher') || $user->hasRole('Student') || $user->hasRole('Parent')) {
            return false;
        }

        return $user->hasRole('Super Admin') || $user->hasRole('Branch Admin') || $user->hasPermission(PermissionDictionary::ONBOARDING_MANAGE);
    }
}
