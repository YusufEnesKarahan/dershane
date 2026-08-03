<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Plan;
use App\Models\Branch;
use App\Models\License;
use App\Models\Subscription;
use App\Models\SubscriptionHistory;
use App\Models\Role;
use App\Domain\Platform\Services\SubscriptionManagementService;
use App\Domain\Platform\Services\SubscriptionSchedulerService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Cache;

class SubscriptionLifecycleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        $superAdminRole = Role::firstOrCreate(['name' => 'Super Admin']);
        $adminRole = Role::firstOrCreate(['name' => 'Admin']);
        
        $this->superAdmin = User::factory()->create();
        $this->superAdmin->roles()->attach($superAdminRole->id);
        
        $this->tenantAdmin = User::factory()->create();
        $this->tenantAdmin->roles()->attach($adminRole->id);
        
        $this->branch = Branch::factory()->create([
            'name' => 'Test Branch',
            'slug' => 'test-branch',
        ]);
        $this->tenantAdmin->update(['branch_id' => $this->branch->id]);

        $this->license = License::create([
            'license_key' => 'TEST-KEY',
            'type' => 'saas',
            'status' => 'active',
            'starts_at' => now(),
            'expires_at' => now()->addYear(),
        ]);
        
        $this->plan = Plan::create([
            'name' => 'Pro Plan',
            'slug' => 'pro-plan',
            'price' => 500,
            'billing_cycle' => 'monthly',
            'trial_days' => 14,
            'limits' => ['grace_days' => 7],
        ]);
        
        $this->service = app(SubscriptionManagementService::class);
        $this->scheduler = app(SubscriptionSchedulerService::class);
    }

    public function test_trial_to_active_lifecycle()
    {
        // 1. Create Trial
        $subscription = $this->service->createTrialSubscription($this->branch, $this->plan);
        
        $this->assertEquals('trial', $subscription->status);
        $this->assertNotNull($subscription->trial_ends_at);
        $this->assertDatabaseHas('subscription_histories', [
            'subscription_id' => $subscription->id,
            'action' => 'created'
        ]);

        // 2. Change to Active (Renew / Upgrade usually does this)
        $subscription = $this->service->renewSubscription($this->branch);
        
        $this->assertEquals('active', $subscription->status);
        $this->assertDatabaseHas('subscription_histories', [
            'subscription_id' => $subscription->id,
            'action' => 'renewed'
        ]);
    }

    public function test_active_to_grace_to_expired_to_suspended_via_scheduler()
    {
        Event::fake([
            \App\Events\SubscriptionExpired::class,
            \App\Events\SubscriptionSuspended::class,
        ]);

        $subscription = $this->service->assignPlanToTenant($this->branch, $this->plan);
        $subscription->update(['status' => 'active', 'expires_at' => Carbon::now()->addDays(30)]);
        $this->assertEquals('active', $subscription->status);

        // Fast forward to end of active period (simulate expires_at and ends_at passed)
        $subscription->update([
            'ends_at' => Carbon::now()->subDay(),
            'expires_at' => Carbon::now()->subDay(),
        ]);
        
        $this->scheduler->processDailyChecks();
        $subscription->refresh();

        // Should be in grace because plan has grace_days = 7
        $this->assertEquals('grace', $subscription->status);
        $this->assertDatabaseHas('subscription_histories', [
            'subscription_id' => $subscription->id,
            'action' => 'grace_started'
        ]);

        // Fast forward past grace period
        $subscription->update(['expires_at' => Carbon::now()->subDays(8)]);
        
        $this->scheduler->processDailyChecks();
        $subscription->refresh();

        // Should be expired now
        $this->assertEquals('expired', $subscription->status);
        Event::assertDispatched(\App\Events\SubscriptionExpired::class);

        // Fast forward 31 days past expiry
        $subscription->update(['expires_at' => Carbon::now()->subDays(40)]);
        
        $this->scheduler->processDailyChecks();
        $subscription->refresh();

        // Should be suspended
        $this->assertEquals('suspended', $subscription->status);
        Event::assertDispatched(\App\Events\SubscriptionSuspended::class);
    }
}
