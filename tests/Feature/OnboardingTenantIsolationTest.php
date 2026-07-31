<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\HQTenant;
use App\Models\HQOnboardingFlow;

class OnboardingTenantIsolationTest extends TestCase
{
    use RefreshDatabase;

    public function test_onboarding_flow_is_tied_to_correct_tenant()
    {
        $tenant1 = HQTenant::create(['name' => 'Tenant 1', 'slug' => 'tenant-1']);
        $tenant2 = HQTenant::create(['name' => 'Tenant 2', 'slug' => 'tenant-2']);

        $flow1 = HQOnboardingFlow::create(['tenant_id' => $tenant1->id, 'current_step' => 'step1']);
        $flow2 = HQOnboardingFlow::create(['tenant_id' => $tenant2->id, 'current_step' => 'step1']);

        $this->assertEquals($tenant1->id, $flow1->tenant_id);
        $this->assertEquals($tenant2->id, $flow2->tenant_id);
        $this->assertNotEquals($flow1->tenant_id, $flow2->tenant_id);
    }
}
