<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\HQTenant;
use App\Domain\HQ\Services\Governance\RiskEngineService;

class RiskEngineTest extends TestCase
{
    use RefreshDatabase;

    public function test_calculates_risk_score_dynamically()
    {
        $tenant = HQTenant::create(['name' => 'Risk Tenant', 'slug' => 'r1', 'status' => 'active', 'domain' => 'r1.com']);
        $service = app(RiskEngineService::class);

        $result = $service->calculateRisk($tenant, [
            'backup_failed' => true,
            'status' => 'online',
        ]);

        $this->assertEquals(15, $result->score);
        $this->assertEquals('healthy', $result->level); // < 20 is healthy

        $criticalResult = $service->calculateRisk($tenant, [
            'license_expired' => true,
            'status' => 'offline',
        ]);

        $this->assertEquals(55, $criticalResult->score);
        $this->assertEquals('critical', $criticalResult->level);
    }
}
