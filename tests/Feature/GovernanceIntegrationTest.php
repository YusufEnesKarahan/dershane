<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\HQTenant;
use App\Domain\HQ\Services\HQSchedulerService;

class GovernanceIntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_scheduler_runs_governance_checks_without_fatal_errors()
    {
        $tenant = HQTenant::create(['name' => 'Gov Tenant', 'slug' => 'g1', 'status' => 'active', 'domain' => 'g1.com']);
        
        $scheduler = app(HQSchedulerService::class);
        
        // Use reflection to run the protected method
        $reflection = new \ReflectionClass(HQSchedulerService::class);
        $method = $reflection->getMethod('runGovernanceChecks');
        $method->setAccessible(true);
        
        $method->invoke($scheduler);

        // Assert that the risk score was generated during the scheduler run
        $this->assertTrue(\App\Models\HQRiskScore::where('tenant_id', $tenant->id)->exists());
    }
}
