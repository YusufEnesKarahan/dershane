<?php

namespace App\Domain\Portal\Services;

use App\Models\Institution;
use Illuminate\Support\Facades\DB;

class AccountService
{
    public function updateProfile(Institution $tenant, array $data)
    {
        return DB::transaction(function () use ($tenant, $data) {
            $tenant->update([
                'name' => $data['name'] ?? $tenant->name,
                // other updatable fields like contact_email
            ]);
            
            app(\App\Core\Services\AuditService::class)->logSystemAction(
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
