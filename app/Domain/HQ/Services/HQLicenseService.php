<?php

namespace App\Domain\HQ\Services;

use App\Models\HQLicense;
use App\Models\HQLicenseFeature;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class HQLicenseService
{
    /**
     * Create a new license.
     */
    public function createLicense(array $data): HQLicense
    {
        return DB::transaction(function () use ($data) {
            $license = HQLicense::create([
                'tenant_id' => $data['tenant_id'],
                'system_instance_id' => $data['system_instance_id'] ?? null,
                'license_key' => $data['license_key'] ?? 'LIC-' . strtoupper(Str::random(16)),
                'plan' => $data['plan'],
                'status' => $data['status'] ?? 'pending',
                'starts_at' => $data['starts_at'] ?? now(),
                'expires_at' => $data['expires_at'] ?? null,
                'features' => $data['features'] ?? [],
                'metadata' => $data['metadata'] ?? [],
            ]);

            if (isset($data['features']) && is_array($data['features'])) {
                foreach ($data['features'] as $feature => $enabled) {
                    $license->licenseFeatures()->create([
                        'feature_name' => $feature,
                        'enabled' => $enabled
                    ]);
                }
            }

            return $license;
        });
    }

    /**
     * Activate a license.
     */
    public function activateLicense(HQLicense $license): bool
    {
        return $license->update(['status' => 'active']);
    }

    /**
     * Suspend a license.
     */
    public function suspendLicense(HQLicense $license): bool
    {
        return $license->update(['status' => 'suspended']);
    }

    /**
     * Check and update expired licenses. 
     * Can be called by a cron job or middleware occasionally.
     */
    public function checkExpiration(): int
    {
        return HQLicense::where('status', 'active')
            ->whereNotNull('expires_at')
            ->where('expires_at', '<', now())
            ->update(['status' => 'expired']);
    }

    /**
     * Enable a specific feature on a license.
     */
    public function enableFeature(HQLicense $license, string $featureName): HQLicenseFeature
    {
        return $license->licenseFeatures()->updateOrCreate(
            ['feature_name' => $featureName],
            ['enabled' => true]
        );
    }

    /**
     * Disable a specific feature on a license.
     */
    public function disableFeature(HQLicense $license, string $featureName): HQLicenseFeature
    {
        return $license->licenseFeatures()->updateOrCreate(
            ['feature_name' => $featureName],
            ['enabled' => false]
        );
    }
}
