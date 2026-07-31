<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\HQTenant;
use App\Models\HQComplianceFramework;
use App\Models\HQPolicy;
use App\Domain\HQ\Services\Governance\ComplianceService;

class ComplianceEngineTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_evaluate_compliance_framework()
    {
        $tenant = HQTenant::create(['name' => 'Tenant 1', 'slug' => 't1', 'status' => 'active', 'domain' => 't1.com']);
        $framework = HQComplianceFramework::create(['name' => 'ISO27001', 'version' => '2022']);
        
        $policy = HQPolicy::create([
            'name' => 'A.9.2.1 Password Policy',
            'type' => 'compliance',
            'logic' => ['metric' => 'password_strength', 'operator' => '>=', 'value' => 8]
        ]);

        $service = app(ComplianceService::class);
        $service->addControl($framework, [
            'control_code' => 'A.9.2.1',
            'title' => 'Password Rules',
            'policy_id' => $policy->id
        ]);

        $result = $service->evaluateTenantCompliance($tenant, $framework, ['password_strength' => 10]);
        $this->assertEquals(100, $result->score_percentage);
        $this->assertEquals('passed', $result->details['A.9.2.1']['status']);

        $failedResult = $service->evaluateTenantCompliance($tenant, $framework, ['password_strength' => 6]);
        $this->assertEquals(0, $failedResult->score_percentage);
        $this->assertEquals('failed', $failedResult->details['A.9.2.1']['status']);
    }
}
