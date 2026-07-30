<?php

namespace App\Domain\HQ\Services\IAM;

use App\Models\User;
use App\Models\HQRole;
use App\Models\HQPermission;
use App\Domain\HQ\Services\HQAuditService;

class HQPermissionService
{
    public function hasPermission(User $user, string $permissionSlug): bool
    {
        // Super admin has all permissions
        if ($this->hasRole($user, 'super-admin')) {
            return true;
        }

        return HQRole::whereHas('users', fn($q) => $q->where('users.id', $user->id))
            ->whereHas('permissions', fn($q) => $q->where('slug', $permissionSlug))
            ->exists();
    }

    public function hasRole(User $user, string $roleSlug): bool
    {
        return HQRole::where('slug', $roleSlug)
            ->whereHas('users', fn($q) => $q->where('users.id', $user->id))
            ->exists();
    }

    public function assignRole(User $user, HQRole $role): void
    {
        if (!$this->hasRole($user, $role->slug)) {
            $role->users()->attach($user->id);
            
            app(HQAuditService::class)->logSystemAction(
                action: 'role_assigned',
                category: 'iam',
                severity: 'info',
                description: "Role {$role->name} assigned to user {$user->id}"
            );
            
            event(new \App\Events\RoleAssigned($user, $role));
        }
    }

    public function removeRole(User $user, HQRole $role): void
    {
        if ($this->hasRole($user, $role->slug)) {
            $role->users()->detach($user->id);
            
            app(HQAuditService::class)->logSystemAction(
                action: 'role_removed',
                category: 'iam',
                severity: 'warning',
                description: "Role {$role->name} removed from user {$user->id}"
            );
        }
    }

    public function syncPermissions(HQRole $role, array $permissionIds): void
    {
        $role->permissions()->sync($permissionIds);
        
        app(HQAuditService::class)->logSystemAction(
            action: 'permissions_synced',
            category: 'iam',
            severity: 'info',
            description: "Permissions synced for role {$role->name}"
        );
        
        event(new \App\Events\PermissionChanged($role));
    }

    public function checkTenantAccess(User $user, int $tenantId): bool
    {
        if ($this->hasRole($user, 'super-admin') || $this->hasRole($user, 'platform-admin')) {
            return true;
        }

        // Implement logic to verify tenant association
        // Assuming user model has a tenant_id or tenants relation
        // For simplicity, let's assume it checks a property if set:
        return $user->tenant_id === $tenantId;
    }
}
