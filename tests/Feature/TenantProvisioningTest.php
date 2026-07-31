<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\HQTenant;
use App\Models\User;
use App\Domain\Onboarding\Services\TenantProvisioningService;
use App\Domain\HQ\Services\Billing\SubscriptionService;
use App\Domain\HQ\Services\HQAuditService;

class TenantProvisioningTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_provision_admin_user()
    {
        $tenant = HQTenant::create(['name' => 'Test Tenant', 'slug' => 'test-tenant', 'status' => 'provisioning']);
        
        $service = app(TenantProvisioningService::class);
        $user = $service->createAdminUser($tenant, [
            'name' => 'Admin User',
            'email' => 'admin@test.com',
            'password' => 'secret123'
        ]);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('admin@test.com', $user->email);
        $this->assertDatabaseHas('users', ['email' => 'admin@test.com']);
    }

    public function test_can_activate_portal()
    {
        $tenant = HQTenant::create(['name' => 'Test Tenant', 'slug' => 'test-tenant', 'status' => 'provisioning']);
        
        $service = app(TenantProvisioningService::class);
        $service->activatePortal($tenant);

        $this->assertEquals('active', $tenant->fresh()->status);
    }
}
