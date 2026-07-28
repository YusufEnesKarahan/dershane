<?php

namespace App\Domain\Platform\Services;

use App\Models\SystemIdentity;

class HQIntegrationService
{
    public function __construct(
        protected UpdateService $updateService,
        protected LicenseService $licenseService,
        protected FeatureFlagService $featureFlagService
    ) {}

    /**
     * Get or create the local system identity for HQ integration.
     */
    public function getInstanceInformation(): SystemIdentity
    {
        $identity = SystemIdentity::first();

        if (!$identity) {
            $license = $this->getLicenseStatus();
            $identity = SystemIdentity::create([
                'product_name' => 'Dershane ERP',
                'product_version' => $this->getSystemVersion(),
                'license_key' => $license['key'] ?? null,
                'branch_count' => 1, // Defaulting to 1 for now
            ]);
        } else {
            // Keep it updated if needed (lazy sync)
            $license = $this->getLicenseStatus();
            $identity->update([
                'product_version' => $this->getSystemVersion(),
                'license_key' => $license['key'] ?? $identity->license_key,
            ]);
        }

        return $identity;
    }

    /**
     * Get current application version.
     */
    public function getSystemVersion(): string
    {
        return $this->updateService->currentVersion();
    }

    /**
     * Get current license information.
     */
    public function getLicenseStatus(): array
    {
        return $this->licenseService->checkLicense();
    }

    /**
     * Get a list of enabled feature flags.
     */
    public function getEnabledFeatures(): array
    {
        return $this->featureFlagService->getAllFlags()
            ->where('enabled', true)
            ->pluck('name')
            ->toArray();
    }

    /**
     * Get a simple local health summary.
     */
    public function getHealthSummary(): array
    {
        return [
            'status' => 'Healthy',
            'last_check' => now()->toDateTimeString(),
            'database' => 'Connected',
        ];
    }
}
