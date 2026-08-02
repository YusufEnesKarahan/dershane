<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Plan;
use App\Models\License;
use App\Domain\Platform\Services\SubscriptionService;
use Carbon\Carbon;

class SubscriptionTest extends TestCase
{
    use RefreshDatabase;

    protected SubscriptionService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed', ['--class' => 'PlanSeeder']);
        $this->setupSaaSTenant();
        $this->service = app(SubscriptionService::class);
    }

    public function test_trial_subscription_starts_correctly()
    {
        $license = License::create([
            'license_key' => 'TEST-LIC',
            'status' => 'demo'
        ]);
        
        $plan = Plan::where('slug', 'starter')->first();

        $subscription = $this->service->startTrial($license, $plan, 14);

        $this->assertDatabaseHas('subscriptions', [
            'id' => $subscription->id,
            'status' => 'trialing',
            'plan_id' => $plan->id,
        ]);

        $this->assertDatabaseHas('subscription_logs', [
            'subscription_id' => $subscription->id,
            'action' => 'trial_started'
        ]);

        $this->assertTrue($subscription->isTrialing());
    }

    public function test_activate_subscription()
    {
        $license = License::create([
            'license_key' => 'TEST-LIC',
            'status' => 'demo'
        ]);
        $plan = Plan::where('slug', 'starter')->first();
        $subscription = $this->service->startTrial($license, $plan);

        $activated = $this->service->activateSubscription($license);

        $this->assertEquals('active', $activated->status);
        $this->assertTrue($activated->isActive());
        $this->assertNull($activated->trial_ends_at);

        $this->assertDatabaseHas('subscription_logs', [
            'subscription_id' => $subscription->id,
            'action' => 'activated'
        ]);
    }

    public function test_change_plan()
    {
        $license = License::create([
            'license_key' => 'TEST-LIC',
            'status' => 'demo'
        ]);
        
        $starter = Plan::where('slug', 'starter')->first();
        $pro = Plan::where('slug', 'professional')->first();
        
        $subscription = $this->service->startTrial($license, $starter);
        $this->service->activateSubscription($license);

        $this->service->changePlan($license, $pro);

        $this->assertDatabaseHas('subscriptions', [
            'id' => $subscription->id,
            'plan_id' => $pro->id,
        ]);

        $this->assertDatabaseHas('subscription_logs', [
            'subscription_id' => $subscription->id,
            'action' => 'upgraded',
            'old_plan_id' => $starter->id,
            'new_plan_id' => $pro->id,
        ]);
    }

    public function test_check_expired_subscriptions()
    {
        $license = License::create([
            'license_key' => 'TEST-LIC',
            'status' => 'demo'
        ]);
        $plan = Plan::where('slug', 'starter')->first();
        
        $subscription = $this->service->startTrial($license, $plan);
        $this->service->activateSubscription($license, null, 1);

        // Manually set ends_at to past
        $subscription->update(['ends_at' => Carbon::now()->subDay()]);

        $count = $this->service->checkExpiredSubscriptions();

        $this->assertEquals(1, $count);
        $this->assertDatabaseHas('subscriptions', [
            'id' => $subscription->id,
            'status' => 'expired'
        ]);
    }
}
