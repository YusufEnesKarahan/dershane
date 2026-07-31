<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\HQPlan;
use App\Models\HQTenant;
use App\Domain\HQ\Services\Billing\SubscriptionService;
use App\Domain\HQ\Services\Billing\EntitlementService;
use App\Domain\HQ\Services\HQAuditService;

class SubscriptionLifecycleTest extends TestCase
{
    use RefreshDatabase;

    protected $subscriptionService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->subscriptionService = new SubscriptionService(
            new EntitlementService(),
            new HQAuditService()
        );
    }

    public function test_tenant_can_subscribe_to_plan()
    {
        $tenant = HQTenant::create(['name' => 'Test Tenant', 'slug' => 'test-tenant']);
        $plan = HQPlan::create(['name' => 'Basic', 'slug' => 'basic']);

        $subscription = $this->subscriptionService->subscribe($tenant, $plan);

        $this->assertDatabaseHas('hq_subscriptions', [
            'tenant_id' => $tenant->id,
            'plan_id' => $plan->id,
            'status' => 'active'
        ]);
    }

    public function test_tenant_can_upgrade_subscription()
    {
        $tenant = HQTenant::create(['name' => 'Test Tenant', 'slug' => 'test-tenant']);
        $basicPlan = HQPlan::create(['name' => 'Basic', 'slug' => 'basic']);
        $proPlan = HQPlan::create(['name' => 'Pro', 'slug' => 'pro']);

        $subscription = $this->subscriptionService->subscribe($tenant, $basicPlan);
        
        $this->subscriptionService->upgradeOrDowngrade($subscription, $proPlan);

        $this->assertDatabaseHas('hq_subscriptions', [
            'id' => $subscription->id,
            'plan_id' => $proPlan->id
        ]);
    }

    public function test_tenant_can_cancel_subscription()
    {
        $tenant = HQTenant::create(['name' => 'Test Tenant', 'slug' => 'test-tenant']);
        $plan = HQPlan::create(['name' => 'Basic', 'slug' => 'basic']);

        $subscription = $this->subscriptionService->subscribe($tenant, $plan);
        $this->subscriptionService->cancel($subscription);

        $this->assertDatabaseHas('hq_subscriptions', [
            'id' => $subscription->id,
            'status' => 'cancelled'
        ]);
    }
}
