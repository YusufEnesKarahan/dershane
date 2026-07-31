<?php

namespace App\Domain\Portal\Services;

use App\Models\HQTenant;
use Illuminate\Support\Facades\DB;

class AccountService
{
    public function updateProfile(HQTenant $tenant, array $data)
    {
        return DB::transaction(function () use ($tenant, $data) {
            $tenant->update([
                'name' => $data['name'] ?? $tenant->name,
                // other updatable fields like contact_email
            ]);
            
            app(\App\Domain\HQ\Services\HQAuditService::class)->logSystemAction(
                action: 'portal_profile_updated',
                category: 'portal',
                severity: 'info',
                description: "Tenant {$tenant->id} updated their profile.",
                metadata: ['tenant_id' => $tenant->id]
            );

            return $tenant;
        });
    }
}
