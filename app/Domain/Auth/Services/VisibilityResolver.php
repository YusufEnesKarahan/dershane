<?php
namespace App\Domain\Auth\Services;

use App\Models\User;

class VisibilityResolver
{
    public function __construct(protected PermissionResolver $resolver) {}

    public function resolve(User $user, string $permission, ?string $edition = null, ?string $feature = null): bool
    {
        // 1. Permission Check
        if (!$this->resolver->hasPermission($user, $permission)) {
            return false;
        }

        // 2. Edition Check
        if ($edition && function_exists('edition')) {
            $currentEdition = edition()->current();
            if ($edition === 'professional' && $currentEdition === 'basic') {
                return false;
            }
            if ($edition === 'ultimate' && $currentEdition !== 'ultimate') {
                return false;
            }
        }

        // 3. Feature Check
        if ($feature) {
            if (function_exists('feature_enabled') && !feature_enabled($feature)) {
                return false;
            }
            if (function_exists('feature') && !feature()->active($feature)) {
                return false;
            }
        }

        return true;
    }
}
