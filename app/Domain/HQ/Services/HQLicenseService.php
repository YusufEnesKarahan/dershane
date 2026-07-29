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

            \App\Events\LicenseChanged::dispatch('license.created', $license, null, $license->toArray());

            return $license;
        });
    }

    /**
     * Activate a license.
     */
    public function activateLicense(HQLicense $license): bool
    {
        $res = $license->update(['status' => 'active']);
        \App\Events\LicenseChanged::dispatch('license.activated', $license, ['status' => 'suspended'], ['status' => 'active']);
        return $res;
    }

    /**
     * Suspend a license.
     */
    public function suspendLicense(HQLicense $license): bool
    {
        $res = $license->update(['status' => 'suspended']);
        \App\Events\LicenseChanged::dispatch('license.suspended', $license, ['status' => 'active'], ['status' => 'suspended']);
        return $res;
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
        $old = $license->licenseFeatures()->where('feature_name', $featureName)->first()?->enabled ?? null;
        
        $feature = $license->licenseFeatures()->updateOrCreate(
            ['feature_name' => $featureName],
            ['enabled' => true]
        );
        
        \App\Events\LicenseChanged::dispatch('license.feature_enabled', $license, ['enabled' => $old], ['enabled' => true, 'feature' => $featureName]);

        return $feature;
    }

    public function disableFeature(HQLicense $license, string $featureName): HQLicenseFeature
    {
        $old = $license->licenseFeatures()->where('feature_name', $featureName)->first()?->enabled ?? null;
        
        $feature = $license->licenseFeatures()->updateOrCreate(
            ['feature_name' => $featureName],
            ['enabled' => false]
        );
        
        \App\Events\LicenseChanged::dispatch('license.feature_disabled', $license, ['enabled' => $old], ['enabled' => false, 'feature' => $featureName]);

        return $feature;
    }
}
