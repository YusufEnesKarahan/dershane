<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\HQTenant;
use App\Models\HQPlan;
use App\Domain\HQ\Services\Billing\SubscriptionService;
use App\Domain\HQ\Services\Billing\EntitlementService;
use App\Domain\HQ\Services\HQAuditService;

class EntitlementTest extends TestCase
{
    use RefreshDatabase;

    protected $subscriptionService;
    protected $entitlementService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->entitlementService = new EntitlementService();
        $this->subscriptionService = new SubscriptionService(
            $this->entitlementService,
            new HQAuditService()
        );
    }

    public function test_tenant_gets_plan_entitlements()
    {
        $tenant = HQTenant::create(['name' => 'Test Tenant', 'slug' => 'test-tenant']);
        $plan = HQPlan::create([
            'name' => 'Pro',
            'slug' => 'pro',
            'features' => ['advanced_analytics'],
            'limits' => ['api_calls' => 1000]
        ]);

        $this->subscriptionService->subscribe($tenant, $plan);

        $this->assertTrue($this->entitlementService->hasAccess($tenant, 'advanced_analytics'));
        $this->assertFalse($this->entitlementService->hasAccess($tenant, 'non_existent_feature'));
        $this->assertEquals(1000, $this->entitlementService->getLimit($tenant, 'api_calls'));
    }

    public function test_tenant_loses_entitlements_on_cancel()
    {
        $tenant = HQTenant::create(['name' => 'Test Tenant', 'slug' => 'test-tenant']);
        $plan = HQPlan::create([
            'name' => 'Pro',
            'slug' => 'pro',
            'features' => ['advanced_analytics'],
        ]);

        $subscription = $this->subscriptionService->subscribe($tenant, $plan);
        $this->assertTrue($this->entitlementService->hasAccess($tenant, 'advanced_analytics'));

        $this->subscriptionService->cancel($subscription);
        $this->assertFalse($this->entitlementService->hasAccess($tenant, 'advanced_analytics'));
    }
}
