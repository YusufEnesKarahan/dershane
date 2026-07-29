<?php

namespace App\Domain\HQ\Services;

use App\Models\HQVersion;
use Illuminate\Support\Collection;

class HQVersionService
{
    /**
     * Publish a new version.
     */
    public function publishVersion(array $data): HQVersion
    {
        $data['status'] = 'published';
        $data['published_at'] = now();
        
        return HQVersion::create($data);
    }

    /**
     * Draft a new version.
     */
    public function draftVersion(array $data): HQVersion
    {
        $data['status'] = 'draft';
        return HQVersion::create($data);
    }

    /**
     * Archive an existing version.
     */
    public function archiveVersion(HQVersion $version): HQVersion
    {
        $version->update(['status' => 'archived']);
        return $version;
    }

    /**
     * Get the latest published stable version.
     */
    public function getLatestStableVersion(): ?HQVersion
    {
        return HQVersion::where('status', 'published')
            ->where('channel', 'stable')
            ->orderByDesc('version')
            ->first();
    }

    /**
     * Check if a mandatory update is required for a given version.
     */
    public function checkIfMandatoryUpdateRequired(string $currentVersion): bool
    {
        $latestMandatory = HQVersion::where('status', 'published')
            ->where('is_mandatory', true)
            ->orderByDesc('version')
            ->first();

        if (!$latestMandatory) {
            return false;
        }

        // Simplistic version comparison: if current is less than the latest mandatory version
        return version_compare($currentVersion, $latestMandatory->version, '<');
    }
}
