<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\HQTenant;
use App\Models\HQSystemInstance;

class HQDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);
        config(['app.installed' => true]);
    }

    protected function getSuperAdmin()
    {
        $admin = User::factory()->create();
        $admin->roles()->attach(Role::where('name', 'Super Admin')->first()->id);
        return $admin;
    }

    protected function getStandardUser()
    {
        $user = User::factory()->create();
        $user->roles()->attach(Role::where('name', 'Teacher')->first()->id);
        return $user;
    }

    public function test_dashboard_access_and_authorization()
    {
        // Unauthenticated
        $this->get('/admin/platform/hq-central')->assertRedirect('/login');

        // Standard User (No Super Admin)
        $this->actingAs($this->getStandardUser())
            ->get('/admin/platform/hq-central')
            ->assertStatus(403);

        // Super Admin
        $this->actingAs($this->getSuperAdmin())
            ->get('/admin/platform/hq-central')
            ->assertStatus(200)
            ->assertSee('HQ Central Platform');
    }

    public function test_metrics_calculation_and_offline_detection()
    {
        $tenant = HQTenant::create(['name' => 'Metrics Tenant', 'slug' => 'mt']);
        
        // Online instance
        HQSystemInstance::create([
            'tenant_id' => $tenant->id,
            'system_uuid' => 'sys-1',
            'system_name' => 'Sys 1',
            'system_version' => '1.0',
            'environment' => 'production',
            'status' => 'online',
            'last_seen_at' => now(),
        ]);

        // Instance that should be marked offline (last seen 20 mins ago)
        HQSystemInstance::create([
            'tenant_id' => $tenant->id,
            'system_uuid' => 'sys-2',
            'system_name' => 'Sys 2',
            'system_version' => '1.0',
            'environment' => 'production',
            'status' => 'online', // Currently marked online but stale
            'last_seen_at' => now()->subMinutes(20),
        ]);

        $response = $this->actingAs($this->getSuperAdmin())->get('/admin/platform/hq-central');
        $response->assertStatus(200);
        
        // Assert view has correct metrics
        $response->assertViewHas('metrics');
        $metrics = $response->viewData('metrics');
        
        $this->assertEquals(2, $metrics['systems']['total']);
        $this->assertEquals(1, $metrics['systems']['online']);
        $this->assertEquals(1, $metrics['systems']['offline']); // Auto updated!

        // Check DB to ensure it was updated
        $offlineSys = HQSystemInstance::where('system_uuid', 'sys-2')->first();
        $this->assertEquals('offline', $offlineSys->status);
    }

    public function test_system_listing()
    {
        $tenant = HQTenant::create(['name' => 'List Tenant', 'slug' => 'lt']);
        HQSystemInstance::create([
            'tenant_id' => $tenant->id,
            'system_uuid' => 'listing-uuid-test',
            'system_name' => 'Listing ERP',
            'system_version' => '1.0',
            'environment' => 'production',
            'status' => 'online',
            'last_seen_at' => now(),
        ]);

        $response = $this->actingAs($this->getSuperAdmin())->get('/admin/platform/hq-central/systems');
        $response->assertStatus(200);
        $response->assertSee(substr('listing-uuid-test', 0, 8));
        $response->assertSee('Listing ERP');
    }

    public function test_tenant_listing_and_creation()
    {
        $admin = $this->getSuperAdmin();

        $response = $this->actingAs($admin)->get('/admin/platform/hq-central/tenants');
        $response->assertStatus(200);

        // Test Creation
        $payload = [
            'name' => 'New Corp',
            'slug' => 'new-corp',
            'status' => 'active'
        ];

        $postResponse = $this->actingAs($admin)->post('/admin/platform/hq-central/tenants', $payload);
        $postResponse->assertRedirect(route('admin.platform.hq_central.tenants.index'));

        $this->assertDatabaseHas('hq_tenants', [
            'slug' => 'new-corp',
            'name' => 'New Corp'
        ]);
    }
}
