<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\HQTenant;
use App\Models\HQLicense;
use App\Domain\HQ\Services\HQLicenseService;

class HQLicenseTest extends TestCase
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

    public function test_license_creation_and_authorization()
    {
        $tenant = HQTenant::create(['name' => 'License Corp', 'slug' => 'lc']);
        
        $payload = [
            'tenant_id' => $tenant->id,
            'plan' => 'Enterprise',
            'status' => 'active',
        ];

        // Standard user blocked
        $this->actingAs($this->getStandardUser())
            ->post('/admin/platform/hq-central/licenses', $payload)
            ->assertStatus(403);

        // Super Admin allowed
        $this->actingAs($this->getSuperAdmin())
            ->post('/admin/platform/hq-central/licenses', $payload)
            ->assertRedirect();

        $this->assertDatabaseHas('hq_licenses', [
            'tenant_id' => $tenant->id,
            'plan' => 'Enterprise',
            'status' => 'active'
        ]);
    }

    public function test_license_activation_and_suspension()
    {
        $tenant = HQTenant::create(['name' => 'State Test', 'slug' => 'st']);
        $license = HQLicense::create([
            'tenant_id' => $tenant->id,
            'plan' => 'Pro',
            'status' => 'pending'
        ]);

        $admin = $this->getSuperAdmin();

        // Activate
        $this->actingAs($admin)->post("/admin/platform/hq-central/licenses/{$license->id}/activate");
        $this->assertEquals('active', $license->fresh()->status);

        // Suspend
        $this->actingAs($admin)->post("/admin/platform/hq-central/licenses/{$license->id}/suspend");
        $this->assertEquals('suspended', $license->fresh()->status);
    }

    public function test_automatic_expiration()
    {
        $tenant = HQTenant::create(['name' => 'Expire Test', 'slug' => 'et']);
        
        HQLicense::create([
            'tenant_id' => $tenant->id,
            'plan' => 'Pro',
            'status' => 'active',
            'expires_at' => now()->subDay()
        ]);
        
        $service = app(HQLicenseService::class);
        $updated = $service->checkExpiration();
        
        $this->assertEquals(1, $updated);
        $this->assertDatabaseHas('hq_licenses', ['status' => 'expired']);
    }

    public function test_feature_toggle()
    {
        $tenant = HQTenant::create(['name' => 'Feature Test', 'slug' => 'ft']);
        $license = HQLicense::create([
            'tenant_id' => $tenant->id,
            'plan' => 'Basic',
            'status' => 'active'
        ]);

        $admin = $this->getSuperAdmin();

        // Enable feature
        $this->actingAs($admin)->post("/admin/platform/hq-central/licenses/{$license->id}/features", [
            'feature_name' => 'premium_module',
            'enabled' => '1'
        ]);

        $this->assertDatabaseHas('hq_license_features', [
            'license_id' => $license->id,
            'feature_name' => 'premium_module',
            'enabled' => 1
        ]);

        // Disable feature
        $this->actingAs($admin)->post("/admin/platform/hq-central/licenses/{$license->id}/features", [
            'feature_name' => 'premium_module',
            'enabled' => '0'
        ]);

        $this->assertDatabaseHas('hq_license_features', [
            'license_id' => $license->id,
            'feature_name' => 'premium_module',
            'enabled' => 0
        ]);
    }
}
