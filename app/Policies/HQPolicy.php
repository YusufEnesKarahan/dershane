<?php

namespace App\Policies;

use App\Models\User;

class HQPolicy
{
    /**
     * Determine if the user can view the HQ dashboard and systems.
     */
    public function viewDashboard(User $user): bool
    {
        return $user->hasRole('Super Admin');
    }

    /**
     * Determine if the user can manage tenants.
     */
    public function manageTenant(User $user): bool
    {
        return $user->hasRole('Super Admin');
    }

    /**
     * Determine if the user can send commands to instances.
     */
    public function sendCommand(User $user): bool
    {
        return $user->hasRole('Super Admin');
    }

    /**
     * Determine if the user can manage licenses.
     */
    public function manageLicense(User $user): bool
    {
        return $user->hasRole('Super Admin');
    }

    /**
     * Determine if the user can view audit logs.
     */
    public function viewAuditLogs(User $user): bool
    {
        return $user->hasRole('Super Admin') || $user->hasPermission('hq.viewAuditLogs');
    }
}
