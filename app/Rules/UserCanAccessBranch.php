<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class UserCanAccessBranch implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $user = auth()->user();

        if (!$user) {
            $fail('The :attribute is invalid.');
            return;
        }

        // Check if the branch exists
        if (!\App\Models\Branch::where('id', $value)->exists()) {
            $fail('The selected branch does not exist.');
            return;
        }

        // If user is super admin, they can access any branch
        // Assuming there's a hasRole method or similar
        if (method_exists($user, 'hasRole') && $user->hasRole('super-admin')) {
            return;
        }

        // If user has a specific branch assigned, they must only use that branch
        if ($user->branch_id && $user->branch_id != $value) {
            $fail('You do not have permission to access this branch.');
        }
    }
}
