<?php

namespace App\Domain\Platform\Services;

use App\Models\License;
use App\Models\Student;
use App\Models\Branch;

class LicenseLimitService
{
    protected ?License $activeLicense = null;

    public function __construct()
    {
        $this->activeLicense = License::whereIn('status', ['active', 'trial'])->first();
    }

    /**
     * Get the active license.
     */
    public function getLicense(): ?License
    {
        return $this->activeLicense;
    }

    /**
     * Check if the system has an active license.
     */
    public function hasActiveLicense(): bool
    {
        return $this->activeLicense !== null && $this->activeLicense->isActive();
    }

    /**
     * Get a specific limit from the license metadata.
     * If the limit is not explicitly set, returns PHP_INT_MAX (no limit).
     */
    public function getLimit(string $key, int $default = PHP_INT_MAX): int
    {
        if (!$this->activeLicense || !isset($this->activeLicense->metadata[$key])) {
            return $default;
        }

        return (int) $this->activeLicense->metadata[$key];
    }

    /**
     * Check if a new student can be added.
     */
    public function canAddStudent(): bool
    {
        $limit = $this->getLimit('max_students');
        if ($limit === PHP_INT_MAX) {
            return true;
        }

        // We count all students across all branches because the license applies to the whole installation.
        // But since Student has a Global Scope (BranchScope), we need to bypass it to get the true total count.
        $currentCount = Student::withoutGlobalScopes()->count();
        
        return $currentCount < $limit;
    }

    /**
     * Check if a new branch can be added.
     */
    public function canAddBranch(): bool
    {
        $limit = $this->getLimit('max_branches');
        if ($limit === PHP_INT_MAX) {
            return true;
        }

        $currentCount = Branch::count();
        
        return $currentCount < $limit;
    }
}
