<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\License;
use App\Models\FeatureFlag;
use App\Models\Role;
use Illuminate\Support\Str;
use App\Domain\Platform\Services\LicenseService;

class SaaSControlLayerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);
    }

    public function test_license_middleware_active_test()
    {
        License::create([
            'license_key' => Str::uuid()->toString(),
            'status' => 'active',
            'expires_at' => now()->addYear(),
        ]);
        app(LicenseService::class)->clearCache();

        $user = User::factory()->create();
        $user->roles()->attach(Role::where('name', 'Teacher')->first()->id);
        
        $response = $this->actingAs($user)->get('/teacher/dashboard');
        
        $this->assertNotEquals(403, $response->status());
    }

    public function test_expired_license_blocked_test()
    {
        License::create([
            'license_key' => Str::uuid()->toString(),
            'status' => 'expired',
        ]);
        app(LicenseService::class)->clearCache();

        $user = User::factory()->create();
        $user->roles()->attach(Role::where('name', 'Teacher')->first()->id);
        
        $response = $this->actingAs($user)->get('/teacher/dashboard');
        
        $response->assertStatus(403);
        $this->assertEquals('License expired', $response->exception->getMessage());
    }

    public function test_super_admin_bypass_test()
    {
        License::create([
            'license_key' => Str::uuid()->toString(),
            'status' => 'expired',
        ]);
        app(LicenseService::class)->clearCache();

        $superAdmin = User::factory()->create();
        $superAdmin->roles()->attach(Role::where('name', 'Super Admin')->first()->id);
        
        $response = $this->actingAs($superAdmin)->get('/teacher/dashboard');
        
        $this->assertNotEquals(403, $response->status());
    }

    public function test_feature_flag_toggle_test()
    {
        $superAdmin = User::factory()->create();
        $superAdmin->roles()->attach(Role::where('name', 'Super Admin')->first()->id);

        $feature = FeatureFlag::create([
            'name' => 'test_feature',
            'enabled' => false,
        ]);

        $response = $this->actingAs($superAdmin)->post("/admin/platform/features/{$feature->id}/toggle");
        
        $response->assertRedirect('/admin/platform/features');
        $this->assertTrue($feature->fresh()->enabled);
    }

    public function test_admin_license_page_access_test()
    {
        $superAdmin = User::factory()->create();
        $superAdmin->roles()->attach(Role::where('name', 'Super Admin')->first()->id);

        $response = $this->actingAs($superAdmin)->get('/admin/platform/licenses');
        
        $response->assertStatus(200);
        $response->assertViewIs('admin.platform.licenses.index');
    }
}
