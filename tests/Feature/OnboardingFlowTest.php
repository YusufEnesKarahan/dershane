<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\HQOnboardingFlow;
use App\Models\HQTenant;
use App\Models\HQPlan;
use Illuminate\Support\Facades\Event;

class OnboardingFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_start_and_complete_onboarding_flow()
    {
        Event::fake([\App\Events\TenantRegistered::class]);

        $response = $this->postJson('/api/onboarding/start', [
            'name' => 'Acme Corp',
        ]);

        $response->assertStatus(201);
        $flowId = $response->json('flow_id');

        $flow = HQOnboardingFlow::where('uuid', $flowId)->first();
        $this->assertNotNull($flow);
        $this->assertEquals('tenant_creation', $flow->current_step);

        // Step: Tenant Creation
        $response = $this->postJson('/api/onboarding/step', [
            'flow_id' => $flowId,
            'step' => 'tenant_creation',
            'payload' => ['name' => 'Acme Corp']
        ]);
        $response->assertStatus(200);

        Event::assertDispatched(\App\Events\TenantRegistered::class);

        $flow->refresh();
        $this->assertNotNull($flow->tenant_id);
        $tenantId = $flow->tenant_id;

        // Step: Plan Selection
        $plan = HQPlan::create([
            'name' => 'Pro Plan',
            'slug' => 'pro-plan',
            'stripe_price_id' => 'price_123',
            'price' => 100,
            'currency' => 'USD',
            'billing_cycle' => 'monthly',
        ]);

        $response = $this->postJson('/api/onboarding/step', [
            'flow_id' => $flowId,
            'step' => 'plan_selection',
            'payload' => ['plan_id' => $plan->id]
        ]);
        $response->assertStatus(200);

        // Verify Billing
        $tenant = HQTenant::find($tenantId);
        $this->assertEquals(1, $tenant->subscriptions()->count());

        // Complete
        $response = $this->postJson('/api/onboarding/complete', [
            'flow_id' => $flowId,
        ]);
        $response->assertStatus(200);
    }
}
