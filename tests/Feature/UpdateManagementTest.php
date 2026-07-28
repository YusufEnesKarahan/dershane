<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\UpdatePackage;
use App\Domain\Platform\Services\UpdateService;

class UpdateManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);
    }

    public function test_no_update_available()
    {
        $service = app(UpdateService::class);
        $current = $service->currentVersion();
        
        UpdatePackage::create([
            'name' => 'Test Update',
            'version' => $current, // Same as current
            'release_date' => now(),
        ]);

        $this->assertFalse($service->isUpdateAvailable());
    }

    public function test_update_available()
    {
        $service = app(UpdateService::class);
        
        // Ensure a newer version is generated (e.g. 9.9.9 > current version)
        UpdatePackage::create([
            'name' => 'Major Update',
            'version' => '9.9.9',
            'release_date' => now(),
        ]);

        $this->assertTrue($service->isUpdateAvailable());
    }

    public function test_checksum_success()
    {
        $service = app(UpdateService::class);
        $fileContent = 'dummy_content';
        $localHash = hash('sha256', $fileContent);
        $expectedHash = hash('sha256', 'dummy_content');

        $this->assertTrue($service->verifyChecksum($localHash, $expectedHash));
    }

    public function test_checksum_fail()
    {
        $service = app(UpdateService::class);
        $localHash = hash('sha256', 'dummy_content');
        $expectedHash = hash('sha256', 'different_content');

        $this->assertFalse($service->verifyChecksum($localHash, $expectedHash));
    }

    public function test_admin_update_page_access()
    {
        // Bypass installation check for test
        config(['app.installed' => true]);

        $superAdmin = User::factory()->create();
        $superAdmin->roles()->attach(Role::where('name', 'Super Admin')->first()->id);
        
        $response = $this->actingAs($superAdmin)->get('/admin/platform/updates');
        
        $response->assertStatus(200);
        $response->assertViewIs('admin.platform.updates.index');
        $response->assertViewHas('currentVersion');
        $response->assertViewHas('isUpdateAvailable');
    }
}
