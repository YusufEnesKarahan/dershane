<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\SystemIdentity;
use App\Domain\Platform\Services\HQIntegrationService;
use Illuminate\Support\Str;

class HQIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);
    }

    public function test_system_identity_creation()
    {
        $identity = SystemIdentity::create([
            'product_name' => 'Test Product',
            'branch_count' => 5
        ]);

        $this->assertNotNull($identity->uuid);
        $this->assertNotNull($identity->installation_uuid);
        $this->assertEquals('Test Product', $identity->product_name);
        $this->assertEquals(5, $identity->branch_count);
    }

    public function test_hq_service_works()
    {
        $service = app(HQIntegrationService::class);
        $identity = $service->getInstanceInformation();
        
        $this->assertNotNull($identity);
        $this->assertInstanceOf(SystemIdentity::class, $identity);
        
        $health = $service->getHealthSummary();
        $this->assertEquals('Healthy', $health['status']);
    }

    public function test_uuid_format_is_correct()
    {
        $service = app(HQIntegrationService::class);
        $identity = $service->getInstanceInformation();

        $this->assertTrue(Str::isUuid($identity->uuid));
        $this->assertTrue(Str::isUuid($identity->installation_uuid));
    }

    public function test_admin_page_renders()
    {
        config(['app.installed' => true]);

        $superAdmin = User::factory()->create();
        $superAdmin->roles()->attach(Role::where('name', 'Super Admin')->first()->id);
        
        $response = $this->actingAs($superAdmin)->get('/admin/platform/hq-integration');
        
        $response->assertStatus(200);
        $response->assertViewIs('admin.platform.hq_integration.index');
        $response->assertViewHas('identity');
        $response->assertViewHas('licenseStatus');
    }

    public function test_dashboard_widget_renders_hq_status()
    {
        config(['app.installed' => true]);

        $superAdmin = User::factory()->create();
        $superAdmin->roles()->attach(Role::where('name', 'Super Admin')->first()->id);
        
        $response = $this->actingAs($superAdmin)->get('/admin/reporting/dashboard');
        
        $response->assertStatus(200);
        
        // Assert that the view has the hq_status metric
        $metrics = $response->viewData('metrics');
        $this->assertArrayHasKey('hq_status', $metrics);
        $this->assertArrayHasKey('connected', $metrics['hq_status']);
        $this->assertArrayHasKey('system_uuid', $metrics['hq_status']);
    }
}
