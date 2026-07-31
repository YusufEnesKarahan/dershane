<?php

namespace App\Domain\HQ\Services\Extension;

use App\Models\HQExtension;
use App\Models\HQTenant;
use App\Domain\HQ\Services\Configuration\FeatureFlagService;

class MarketplaceService
{
    protected $featureFlagService;
    protected $dependencyService;

    public function __construct(
        FeatureFlagService $featureFlagService,
        ExtensionDependencyService $dependencyService
    ) {
        $this->featureFlagService = $featureFlagService;
        $this->dependencyService = $dependencyService;
    }

    /**
     * Get a list of all available extensions in the marketplace.
     */
    public function getAvailableExtensions(?HQTenant $tenant = null, array $filters = [])
    {
        $query = HQExtension::query()->with('versions')->where('status', 'active');

        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }
        
        if (!empty($filters['search'])) {
            $query->where('name', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('description', 'like', '%' . $filters['search'] . '%');
        }

        $extensions = $query->get();

        // If a tenant is provided, filter out extensions restricted by Feature Flags
        if ($tenant) {
            $extensions = $extensions->filter(function ($extension) use ($tenant) {
                // If a feature flag exists for this extension, check it.
                // Assuming feature flag is mapped as 'marketplace_allow_' . $extension->slug
                $flagKey = 'marketplace_allow_' . $extension->slug;
                
                // If flag doesn't exist, allow by default. Otherwise check flag logic.
                $flagExists = \App\Models\HQFeatureFlag::where('key', $flagKey)->exists();
                if (!$flagExists) {
                    return true;
                }
                
                return $this->featureFlagService->isEnabled($flagKey, ['tenant_id' => $tenant->id]);
            });
        }

        return $extensions->values();
    }

    /**
     * Get detailed information for a specific extension.
     */
    public function getExtensionDetails(string $slug, array $systemContext = [])
    {
        $extension = HQExtension::with(['versions' => function ($q) {
            $q->orderBy('created_at', 'desc');
        }])->where('slug', $slug)->firstOrFail();

        // Check compatibility for the latest version
        $latestVersion = $extension->versions->first();
        if ($latestVersion) {
            $compatibility = $this->dependencyService->checkCompatibility($latestVersion, $systemContext);
            $extension->setAttribute('is_compatible', $compatibility['is_compatible']);
            $extension->setAttribute('compatibility_issues', $compatibility['issues']);
        }

        return $extension;
    }
}
