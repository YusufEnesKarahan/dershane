<?php

namespace App\Domain\Platform\Services;

use App\Models\UpdatePackage;

class UpdateService
{
    /**
     * Get the current application version.
     */
    public function currentVersion(): string
    {
        // Currently hardcoded to 5.9.0 as per the SaaS standard, 
        // later this can be loaded from config('app.version') or a constant.
        return config('app.version', '5.9.0');
    }

    /**
     * Get the latest update package available locally.
     */
    public function getLatest(): ?UpdatePackage
    {
        return UpdatePackage::orderByDesc('release_date')
            ->orderByDesc('id')
            ->first();
    }

    /**
     * Check if an update is available compared to the current version.
     */
    public function isUpdateAvailable(): bool
    {
        $latest = $this->getLatest();
        
        if (!$latest) {
            return false;
        }

        // version_compare returns 1 if the first version is greater than the second
        return version_compare($latest->version, $this->currentVersion(), '>');
    }

    /**
     * Verify the integrity of a file/content using checksum (SHA-256).
     */
    public function verifyChecksum(string $localFileHash, string $expectedHash): bool
    {
        return hash_equals($expectedHash, $localFileHash);
    }
}
