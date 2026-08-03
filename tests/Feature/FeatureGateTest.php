<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\License;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class FeatureGateTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        \App\Models\SystemIdentity::create(['company_name' => 'Test', 'product_name' => 'Test ERP']);
        \App\Models\AcademicTerm::create(['name' => '2025-2026', 'start_date' => now(), 'end_date' => now()->addYear(), 'is_active' => true]);

        Route::get('/test-feature-sms', function () {
            return 'success';
        })->middleware(['web', 'subscription.feature:sms']);
    }

    public function test_starter_plan_cannot_access_sms_feature()
    {
        $branch = Branch::create(['name' => 'Starter Branch', 'slug' => 'starter-branch']);
        $user = User::factory()->create(['branch_id' => $branch->id]);

        $plan = Plan::create(['name' => 'Starter', 'slug' => 'starter', 'price' => 100, 'is_active' => true, 'features' => ['attendance']]);
        $license = License::create(['license_key' => 'TEST-1', 'status' => 'active', 'plan_id' => $plan->id, 'plan' => $plan->slug]);
        Subscription::create([
            'license_id' => $license->id,
            'branch_id' => $branch->id,
            'plan_id' => $plan->id,
            'status' => 'active',
            'starts_at' => now(),
            'ends_at' => now()->addMonth(),
        ]);

        $this->actingAs($user);
        $response = $this->getJson('/test-feature-sms?channel=sms');

        $response->assertStatus(403);
    }

    public function test_professional_plan_can_access_sms_feature()
    {
        $branch = Branch::create(['name' => 'Pro Branch', 'slug' => 'pro-branch']);
        $user = User::factory()->create(['branch_id' => $branch->id]);

        $plan = Plan::create(['name' => 'Professional', 'slug' => 'pro', 'price' => 500, 'is_active' => true, 'features' => ['sms', 'attendance']]);
        $license = License::create(['license_key' => 'TEST-2', 'status' => 'active', 'plan_id' => $plan->id, 'plan' => $plan->slug]);
        Subscription::create([
            'license_id' => $license->id,
            'branch_id' => $branch->id,
            'plan_id' => $plan->id,
            'status' => 'active',
            'starts_at' => now(),
            'ends_at' => now()->addMonth(),
        ]);

        $this->actingAs($user);
        $response = $this->get('/test-feature-sms?channel=sms');

        $response->assertStatus(200);
        $response->assertSee('success');
    }

    public function test_expired_subscription_blocks_feature_access()
    {
        $branch = Branch::create(['name' => 'Expired Branch', 'slug' => 'expired-branch']);
        $user = User::factory()->create(['branch_id' => $branch->id]);

        $plan = Plan::create(['name' => 'Professional', 'slug' => 'pro', 'price' => 500, 'is_active' => true, 'features' => ['sms', 'attendance']]);
        $license = License::create(['license_key' => 'TEST-3', 'status' => 'expired', 'plan_id' => $plan->id, 'plan' => $plan->slug]);
        Subscription::create([
            'license_id' => $license->id,
            'branch_id' => $branch->id,
            'plan_id' => $plan->id,
            'status' => 'expired',
            'starts_at' => now()->subMonths(2),
            'ends_at' => now()->subMonth(),
            'expires_at' => now()->subMonth(),
        ]);

        $this->actingAs($user);
        $response = $this->getJson('/test-feature-sms?channel=sms');

        $response->assertStatus(403);
    }
}
