<?php

namespace App\Domain\HQ\Services;

use App\Models\HQLicense;
use App\Models\HQSystemInstance;
use Illuminate\Support\Facades\Log;

class HQLicenseValidationService
{
    public function __construct(
        protected HQLicenseService $licenseService
    ) {}

    /**
     * Validate a system's license by UUID and optional license key.
     *
     * @return array The structured license response
     */
    public function validateSystemLicense(string $systemUuid, ?string $licenseKey = null): array
    {
        // Mark any globally expired licenses first
        $this->licenseService->checkExpiration();

        $instance = HQSystemInstance::where('system_uuid', $systemUuid)->first();

        if (!$instance) {
            Log::warning('HQ License Validation: Unknown system_uuid attempted validation', [
                'system_uuid' => $systemUuid,
            ]);
            return $this->buildErrorResponse('invalid', 'System instance not found.');
        }

        // Find the active license for this instance
        $license = $instance->currentLicense;

        if (!$license) {
            // Try to find any license (expired/suspended) to give a meaningful response
            $anyLicense = $instance->licenses()->latest('starts_at')->first();

            if ($anyLicense) {
                return $this->buildLicenseResponse($anyLicense);
            }

            return $this->buildErrorResponse('invalid', 'No license found for this system.');
        }

        // Optional: verify the license key matches if provided
        if ($licenseKey && $license->license_key !== $licenseKey) {
            Log::warning('HQ License Validation: License key mismatch', [
                'system_uuid' => $systemUuid,
                'expected' => substr($license->license_key, 0, 8) . '...',
            ]);
            return $this->buildErrorResponse('invalid', 'License key mismatch.');
        }

        return $this->buildLicenseResponse($license);
    }

    /**
     * Get the features map for a license.
     *
     * @return array<string, bool>
     */
    public function getLicenseFeatures(HQLicense $license): array
    {
        $features = [];

        // Granular features from hq_license_features table
        foreach ($license->licenseFeatures as $feature) {
            $features[$feature->feature_name] = (bool) $feature->enabled;
        }

        // Merge with the bulk JSON features column (granular rows take precedence)
        if (is_array($license->features)) {
            foreach ($license->features as $key => $value) {
                if (!isset($features[$key])) {
                    $features[$key] = (bool) $value;
                }
            }
        }

        return $features;
    }

    /**
     * Build the full license response payload.
     */
    public function buildLicenseResponse(HQLicense $license): array
    {
        return [
            'success' => true,
            'license' => [
                'status' => $license->status,
                'plan' => $license->plan,
                'expires_at' => $license->expires_at?->toIso8601String(),
                'features' => $this->getLicenseFeatures($license),
            ],
        ];
    }

    /**
     * Build an error response.
     */
    protected function buildErrorResponse(string $status, string $message): array
    {
        return [
            'success' => false,
            'license' => [
                'status' => $status,
                'plan' => null,
                'expires_at' => null,
                'features' => [],
            ],
            'message' => $message,
        ];
    }
}
