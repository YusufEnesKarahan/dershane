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

    /**
     * Determine if the user can view alerts.
     */
    public function viewAlerts(User $user): bool
    {
        return $user->hasRole('Super Admin') || $user->hasPermission('hq.viewAlerts');
    }

    /**
     * Determine if the user can manage alerts.
     */
    public function manageAlerts(User $user): bool
    {
        return $user->hasRole('Super Admin');
    }

    /**
     * Determine if the user can view billing information.
     */
    public function viewBilling(User $user): bool
    {
        return $user->hasRole('Super Admin') || $user->hasPermission('hq.viewBilling');
    }

    /**
     * Determine if the user can manage billing/invoices.
     */
    public function manageBilling(User $user): bool
    {
        return $user->hasRole('Super Admin') || $user->hasPermission('hq.manageBilling');
    }

    /**
     * Determine if the user can manage subscription plans.
     */
    public function managePlans(User $user): bool
    {
        return $user->hasRole('Super Admin') || $user->hasPermission('hq.managePlans');
    }

    /**
     * Determine if the user can manage the fleet and orchestrate deployments.
     */
    public function manageFleet(User $user): bool
    {
        return $user->hasRole('Super Admin') || $user->hasPermission('hq.manageFleet');
    }
}
