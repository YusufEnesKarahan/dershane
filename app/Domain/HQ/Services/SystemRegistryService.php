<?php

namespace App\Domain\HQ\Services;

use App\Models\HQSystemInstance;

class SystemRegistryService
{
    public function registerInstance(array $payload): HQSystemInstance
    {
        // For testing, we might not have a tenant yet, so let's pick first or create default
        $tenant = \App\Models\HQTenant::firstOrCreate(
            ['slug' => 'default'],
            ['name' => 'Default Tenant', 'status' => 'active']
        );

        return HQSystemInstance::updateOrCreate(
            ['system_uuid' => $payload['system_uuid']],
            [
                'tenant_id' => $tenant->id,
                'system_name' => $payload['system_name'] ?? 'Unknown',
                'system_version' => $payload['version'] ?? '1.0.0',
                'environment' => $payload['environment'] ?? 'production',
                'status' => 'online',
                'last_seen_at' => now(),
            ]
        );
    }

    public function processHeartbeat(string $systemUuid): bool
    {
        $instance = HQSystemInstance::where('system_uuid', $systemUuid)->first();
        
        if ($instance) {
            $instance->update([
                'last_seen_at' => now(),
                'status' => 'online',
            ]);
            return true;
        }
        return false;
    }
}
