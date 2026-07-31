<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\HQTenant;
use App\Models\HQPlan;
use App\Domain\Onboarding\Services\TenantProvisioningService;

class OnboardingBillingIntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_billing_setup_creates_subscription()
    {
        $tenant = HQTenant::create(['name' => 'Test Tenant', 'slug' => 'test-tenant']);
        $plan = HQPlan::create([
            'name' => 'Enterprise Plan',
            'slug' => 'enterprise-plan',
            'stripe_price_id' => 'price_456',
            'price' => 500,
            'currency' => 'USD',
            'billing_cycle' => 'yearly',
        ]);

        $service = app(TenantProvisioningService::class);
        $service->setupBilling($tenant, $plan);

        $this->assertDatabaseHas('hq_subscriptions', [
            'tenant_id' => $tenant->id,
            'plan_id' => $plan->id,
            'status' => 'active',
        ]);
    }
}
