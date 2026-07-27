<?php

namespace App\Domain\Platform\Services;

use App\Models\License;
use Illuminate\Support\Facades\Cache;

class LicenseService
{
    /**
     * Check the current license and return its status info.
     *
     * @return array{status: string, active: bool, expired: bool, expires_at: ?string}
     */
    public function checkLicense(): array
    {
        $license = $this->getCurrentLicense();

        if (!$license) {
            return [
                'status' => 'no_license',
                'active' => false,
                'expired' => false,
                'expires_at' => null,
            ];
        }

        return [
            'status' => $license->status,
            'active' => $license->isActive(),
            'expired' => $license->isExpired(),
            'expires_at' => $license->expires_at?->toIso8601String(),
        ];
    }

    /**
     * Check if the current license is active.
     */
    public function isActive(): bool
    {
        $license = $this->getCurrentLicense();

        return $license && $license->isActive();
    }

    /**
     * Check if the current license has expired.
     */
    public function isExpired(): bool
    {
        $license = $this->getCurrentLicense();

        if (!$license) {
            return false;
        }

        return $license->isExpired();
    }

    /**
     * Get the current (most recent) license record.
     */
    public function getCurrentLicense(): ?License
    {
        return Cache::remember('current_license', 300, function () {
            return License::latest()->first();
        });
    }

    /**
     * Clear the cached license (call after license updates).
     */
    public function clearCache(): void
    {
        Cache::forget('current_license');
    }
}
