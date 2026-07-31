<?php

namespace App\Domain\HQ\Services\Configuration;

use App\Models\HQEnvironmentProfile;

class EnvironmentProfileService
{
    /**
     * Create or update an environment profile.
     */
    public function setupProfile(string $name, string $slug, array $overrides = []): HQEnvironmentProfile
    {
        return HQEnvironmentProfile::updateOrCreate(
            ['slug' => $slug],
            [
                'name' => $name,
                'overrides' => $overrides,
                'is_active' => true,
            ]
        );
    }

    /**
     * Get specific override configuration for an environment.
     */
    public function getOverride(string $slug, string $configKey)
    {
        $profile = HQEnvironmentProfile::where('slug', $slug)->where('is_active', true)->first();
        
        if (!$profile || empty($profile->overrides)) {
            return null;
        }

        return $profile->overrides[$configKey] ?? null;
    }
}
