<?php
namespace App\Domain\Auth\Services;

use App\Models\User;

class UserPreferenceService
{
    public function updatePreferences(User $user, array $prefs): void
    {
        $current = $user->preferences ?? [];
        $merged = array_merge($current, $prefs);
        $user->update(['preferences' => $merged]);
    }
}
