<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\HQTenant;
use App\Models\HQSlaPolicy;
use App\Models\HQSlaViolation;
use App\Domain\HQ\Services\Governance\SLAService;

class SLATest extends TestCase
{
    use RefreshDatabase;

    public function test_sla_detects_violations_and_triggers_events()
    {
        $tenant = HQTenant::create(['name' => 'SLA Tenant', 'slug' => 's1', 'status' => 'active', 'domain' => 's1.com']);
        $sla = HQSlaPolicy::create([
            'name' => 'Uptime SLA',
            'metric' => 'uptime',
            'operator' => '<',
            'threshold_value' => '99.9',
        ]);

        $service = app(SLAService::class);
        
        \Illuminate\Support\Facades\Event::fake();

        // Pass
        $service->checkSLA($sla, $tenant, '99.95');
        \Illuminate\Support\Facades\Event::assertNotDispatched(\App\Events\SLAViolationDetected::class);
        $this->assertEquals(0, HQSlaViolation::count());

        // Fail
        $service->checkSLA($sla, $tenant, '99.5');
        \Illuminate\Support\Facades\Event::assertDispatched(\App\Events\SLAViolationDetected::class);
        $this->assertEquals(1, HQSlaViolation::count());
    }
}
