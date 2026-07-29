<?php

namespace App\Domain\License\Services;

use App\Models\LicenseCache;
use App\Models\SystemIdentity;
use App\Domain\Platform\Services\HQHttpService;
use Illuminate\Support\Facades\Log;

class LicenseVerificationService
{
    public function __construct(
        protected HQHttpService $httpService
    ) {}

    /**
     * Validate the current system's license against HQ.
     *
     * @return array The raw HQ response
     */
    public function validate(): array
    {
        $identity = SystemIdentity::first();

        if (!$identity) {
            Log::warning('LicenseVerification: No system identity found.');
            return ['success' => false, 'message' => 'No system identity configured.'];
        }

        $payload = [
            'system_uuid' => $identity->uuid,
            'license_key' => $identity->license_key,
            'app_version' => config('app.version', '1.0.0'),
            'timestamp' => now()->toIso8601String(),
        ];

        return $this->httpService->validateLicense($payload);
    }

    /**
     * Refresh the local license cache by calling HQ.
     */
    public function refresh(): bool
    {
        $response = $this->validate();

        if (!isset($response['success'])) {
            Log::warning('LicenseVerification: Invalid response from HQ.', $response);
            return false;
        }

        $identity = SystemIdentity::first();
        $licenseData = $response['license'] ?? [];

        LicenseCache::updateOrCreate(
            ['system_uuid' => $identity?->uuid ?? 'unknown'],
            [
                'license_key' => $identity?->license_key,
                'status' => $licenseData['status'] ?? 'unknown',
                'plan' => $licenseData['plan'] ?? null,
                'features' => $licenseData['features'] ?? [],
                'expires_at' => $licenseData['expires_at'] ?? null,
                'last_checked_at' => now(),
                'metadata' => [
                    'hq_success' => $response['success'],
                    'checked_at' => now()->toIso8601String(),
                ],
            ]
        );

        return (bool) ($response['success'] ?? false);
    }

    /**
     * Get the most recent locally cached license.
     */
    public function getCachedLicense(): ?LicenseCache
    {
        return LicenseCache::latest('last_checked_at')->first();
    }

    /**
     * Check if a specific feature is enabled in the cached license.
     */
    public function hasFeature(string $feature): bool
    {
        $cache = $this->getCachedLicense();

        if (!$cache) {
            // No cache at all — fail open for now (Super Admin controls apply anyway)
            return true;
        }

        return $cache->hasFeature($feature);
    }

    /**
     * Check if the cached license is active.
     */
    public function isActive(): bool
    {
        $cache = $this->getCachedLicense();

        if (!$cache) {
            // No cache — assume active (graceful degradation)
            return true;
        }

        return $cache->isActive();
    }

    /**
     * Get the current plan name from cache.
     */
    public function plan(): ?string
    {
        return $this->getCachedLicense()?->plan;
    }
}
