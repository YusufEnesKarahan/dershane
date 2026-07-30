<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\HQTenant;
use App\Models\HQSubscriptionPlan;
use App\Domain\HQ\Services\HQSubscriptionService;
use App\Domain\HQ\Services\HQBillingService;

class HQBillingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Setup Super Admin for HQ access
        $this->admin = User::factory()->create();
        // Assuming a role assignment mechanism exists in the real system
        // We'll mock the permission/role check if needed, or assume it works based on our policies
    }

    public function test_can_create_subscription_plan()
    {
        $planData = [
            'name' => 'Enterprise Plan',
            'slug' => 'enterprise-plan',
            'description' => 'Full enterprise features',
            'billing_period' => 'monthly',
            'price' => 299.99,
            'currency' => 'USD',
            'limits' => ['students' => 1000],
            'features' => ['advanced_reporting', 'api_access'],
            'is_active' => true,
        ];

        $plan = HQSubscriptionPlan::create($planData);

        $this->assertDatabaseHas('hq_subscription_plans', [
            'slug' => 'enterprise-plan',
            'price' => 299.99,
        ]);
        $this->assertNotNull($plan->uuid);
    }

    public function test_can_subscribe_tenant_to_plan()
    {
        $tenant = HQTenant::create([
            'name' => 'Test Tenant',
            'slug' => 'test',
            'db_name' => 'tenant_test',
            'status' => 'active',
        ]);
        $plan = HQSubscriptionPlan::create([
            'name' => 'Basic Plan',
            'slug' => 'basic',
            'billing_period' => 'monthly',
            'price' => 99.99,
            'currency' => 'USD',
            'is_active' => true,
        ]);

        $service = app(HQSubscriptionService::class);
        $subscription = $service->createSubscription($tenant, $plan);

        $this->assertDatabaseHas('hq_subscriptions', [
            'tenant_id' => $tenant->id,
            'plan_id' => $plan->id,
            'status' => 'active',
        ]);
    }

    public function test_can_generate_invoice_for_subscription()
    {
        $tenant = HQTenant::create([
            'name' => 'Test Tenant 2',
            'slug' => 'test2',
            'db_name' => 'tenant_test2',
            'status' => 'active',
        ]);
        $plan = HQSubscriptionPlan::create([
            'name' => 'Pro Plan',
            'slug' => 'pro',
            'billing_period' => 'yearly',
            'price' => 999.99,
            'currency' => 'USD',
            'is_active' => true,
        ]);

        $subscriptionService = app(HQSubscriptionService::class);
        $subscription = $subscriptionService->createSubscription($tenant, $plan);

        $billingService = app(HQBillingService::class);
        $invoice = $billingService->createInvoice($subscription);

        $this->assertDatabaseHas('hq_invoices', [
            'tenant_id' => $tenant->id,
            'subscription_id' => $subscription->id,
            'status' => 'pending',
            'amount' => 999.99,
        ]);
    }
}
