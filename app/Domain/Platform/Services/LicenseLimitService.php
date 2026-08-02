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
     * Get a specific limit from the active license.
     * Checks Plan limits first, falls back to legacy metadata.
     */
    public function getLimit(string $key, int $default = PHP_INT_MAX): int
    {
        if (!$this->activeLicense) {
            return $default;
        }

        // 1. Plan bazlı limit
        if ($this->activeLicense->planModel && isset($this->activeLicense->planModel->limits[$key])) {
            return (int) $this->activeLicense->planModel->limits[$key];
        }

        // 2. Legacy metadata limiti
        if (isset($this->activeLicense->metadata[$key])) {
            return (int) $this->activeLicense->metadata[$key];
        }

        return $default;
    }

    /**
     * Check if a new student can be added.
     */
    public function canCreateStudent(): bool
    {
        $limit = $this->getLimit('students');
        if ($limit === PHP_INT_MAX) {
            // Fallback for legacy
            $limit = $this->getLimit('max_students', PHP_INT_MAX);
        }

        if ($limit === PHP_INT_MAX) {
            return true;
        }

        $currentCount = Student::withoutGlobalScopes()->count();
        return $currentCount < $limit;
    }

    /**
     * Legacy support
     */
    public function canAddStudent(): bool
    {
        return $this->canCreateStudent();
    }

    /**
     * Check if a new branch can be added.
     */
    public function canCreateBranch(): bool
    {
        $limit = $this->getLimit('branches');
        if ($limit === PHP_INT_MAX) {
            $limit = $this->getLimit('max_branches', PHP_INT_MAX);
        }

        if ($limit === PHP_INT_MAX) {
            return true;
        }

        $currentCount = Branch::count();
        return $currentCount < $limit;
    }

    /**
     * Legacy support
     */
    public function canAddBranch(): bool
    {
        return $this->canCreateBranch();
    }

    /**
     * Check if a new user can be added.
     */
    public function canCreateUser(): bool
    {
        $limit = $this->getLimit('users');
        
        if ($limit === PHP_INT_MAX) {
            return true;
        }

        $currentCount = \App\Models\User::count();
        return $currentCount < $limit;
    }
}
