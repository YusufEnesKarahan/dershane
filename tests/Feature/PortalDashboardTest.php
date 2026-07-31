<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\HQTenant;
use App\Models\User;
use App\Domain\Portal\Services\TenantDashboardService;

class PortalDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_returns_tenant_data()
    {
        $tenant = HQTenant::create([
            'name' => 'Acme Corp',
            'slug' => 'acme-corp',
            'uuid' => \Illuminate\Support\Str::uuid()
        ]);

        $service = app(TenantDashboardService::class);
        $data = $service->getDashboardData($tenant);

        $this->assertIsArray($data);
        $this->assertEquals('Acme Corp', $data['tenant']['name']);
        $this->assertArrayHasKey('subscription', $data);
        $this->assertArrayHasKey('usage', $data);
        $this->assertArrayHasKey('entitlements_summary', $data);
        $this->assertArrayHasKey('recent_activity', $data);
    }
}
