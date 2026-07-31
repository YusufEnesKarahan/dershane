<?php

namespace App\Domain\Onboarding\Services;

use App\Models\HQTenant;
use App\Models\HQPlan;
use App\Models\User;
use App\Models\Role;
use App\Domain\HQ\Services\Billing\SubscriptionService;
use App\Domain\HQ\Services\HQAuditService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class TenantProvisioningService
{
    protected $subscriptionService;
    protected $auditService;

    public function __construct(SubscriptionService $subscriptionService, HQAuditService $auditService)
    {
        $this->subscriptionService = $subscriptionService;
        $this->auditService = $auditService;
    }

    public function createTenant(array $data): HQTenant
    {
        $tenant = HQTenant::create([
            'name' => $data['name'],
            'slug' => \Illuminate\Support\Str::slug($data['name']),
            'status' => 'provisioning', // not active yet
        ]);

        event(new \App\Events\TenantRegistered($tenant));
        $this->auditService->logSystemAction('tenant_created', 'onboarding', 'info', "Tenant {$tenant->name} registered.");

        return $tenant;
    }

    public function createAdminUser(HQTenant $tenant, array $data): User
    {
        // This simulates creating a user within a specific tenant context
        // Depending on DB design, users might be globally unique or tenant-specific. 
        // We'll create a user and assign it a role later in IAM setup.
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        return $user;
    }

    public function setupBilling(HQTenant $tenant, HQPlan $plan)
    {
        $this->subscriptionService->subscribe($tenant, $plan);
        $this->auditService->logSystemAction('billing_setup', 'onboarding', 'info', "Billing setup for Tenant {$tenant->id} with plan {$plan->name}.");
    }

    public function createDefaultConfig(HQTenant $tenant)
    {
        Log::info("TenantProvisioningService: Creating default config for tenant {$tenant->id}");
        // Example: HQConfiguration::create(['tenant_id' => $tenant->id, 'key' => 'timezone', 'value' => 'UTC']);
    }

    public function setupIAM(HQTenant $tenant)
    {
        Log::info("TenantProvisioningService: Setting up IAM for tenant {$tenant->id}");
        // Here we'd map the admin user to the 'Tenant Admin' role for this tenant context.
    }

    public function activatePortal(HQTenant $tenant)
    {
        $tenant->update(['status' => 'active']);
        $this->auditService->logSystemAction('portal_activated', 'onboarding', 'info', "Portal activated for Tenant {$tenant->id}.");
    }

    public function executeTask(\App\Models\HQProvisioningTask $task)
    {
        $tenant = $task->tenant;
        $payload = $task->payload ?? [];

        switch ($task->task_type) {
            case 'create_default_config':
                $this->createDefaultConfig($tenant);
                break;
            case 'setup_iam':
                $this->setupIAM($tenant);
                break;
            // Additional heavy tasks could be handled here
        }
    }
}
