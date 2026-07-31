<?php

namespace App\Domain\HQ\Services\Extension;

use App\Models\HQExtension;
use App\Models\HQTenant;

class ExtensionPermissionService
{
    protected $permissionService;

    public function __construct(\App\Domain\HQ\Services\IAM\HQPermissionService $permissionService)
    {
        $this->permissionService = $permissionService;
    }

    /**
     * Grant permissions to a tenant for a specific extension.
     */
    public function grantPermissions(HQExtension $extension, HQTenant $tenant)
    {
        $permissions = $extension->permissions;
        
        // Find or create a tenant specific role for extensions, or assign to admin
        // We'll assume the tenant has a platform-admin role
        $adminRole = \App\Models\HQRole::where('slug', 'platform-admin')->first();

        if ($adminRole) {
            $permissionIds = $permissions->pluck('id')->toArray();
            
            // Assuming syncPermissions appends or syncs. 
            // In a real scenario, we might want to attach rather than sync to avoid removing existing permissions.
            $adminRole->permissions()->syncWithoutDetaching($permissionIds);
            
            app(\App\Domain\HQ\Services\HQAuditService::class)->logSystemAction(
                action: 'extension_permissions_granted',
                category: 'iam',
                severity: 'info',
                description: "Granted permissions for extension {$extension->slug} to tenant {$tenant->id}"
            );
        }
    }

    /**
     * Revoke permissions from a tenant for a specific extension.
     */
    public function revokePermissions(HQExtension $extension, HQTenant $tenant)
    {
        $permissions = $extension->permissions;
        $adminRole = \App\Models\HQRole::where('slug', 'platform-admin')->first();

        if ($adminRole) {
            $permissionIds = $permissions->pluck('id')->toArray();
            $adminRole->permissions()->detach($permissionIds);
            
            app(\App\Domain\HQ\Services\HQAuditService::class)->logSystemAction(
                action: 'extension_permissions_revoked',
                category: 'iam',
                severity: 'warning',
                description: "Revoked permissions for extension {$extension->slug} from tenant {$tenant->id}"
            );
        }
    }
}
