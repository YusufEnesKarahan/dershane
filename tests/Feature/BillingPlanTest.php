<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\HQPlan;
use App\Models\HQTenant;
use App\Domain\HQ\Services\Billing\BillingPlanService;

class BillingPlanTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_billing_plan()
    {
        $service = new BillingPlanService();

        $plan = $service->createPlan([
            'name' => 'Enterprise',
            'slug' => 'enterprise-plan',
            'description' => 'Full access',
            'type' => 'subscription',
            'price' => 99.99,
            'billing_period' => 'monthly',
            'limits' => ['users' => 100],
            'features' => ['advanced_reporting', 'priority_support'],
        ]);

        $this->assertDatabaseHas('hq_plans', [
            'slug' => 'enterprise-plan',
            'price' => '99.99'
        ]);

        $this->assertEquals(100, $plan->limits['users']);
    }

    public function test_can_update_billing_plan()
    {
        $service = new BillingPlanService();
        $plan = HQPlan::create([
            'name' => 'Free',
            'slug' => 'free-plan',
            'price' => 0,
        ]);

        $updated = $service->updatePlan($plan, ['price' => 10.00]);

        $this->assertEquals(10.00, $updated->price);
    }
}
