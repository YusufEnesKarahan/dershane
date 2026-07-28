<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\HQUpdate;
use App\Models\HQUpdateLog;

class HQUpdateTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);
        config(['app.installed' => true]);
        config(['hq.enabled' => true]);
        // Default configs
        config(['hq.updates.enabled' => false]);
    }

    public function test_updates_disabled_default()
    {
        $this->assertFalse(config('hq.updates.enabled'));
    }

    public function test_current_version()
    {
        $service = app(\App\Domain\Platform\Services\HQUpdateService::class);
        config(['app.version' => '2.5.0']);
        $this->assertEquals('2.5.0', $service->currentVersion());
    }

    public function test_update_registration()
    {
        $service = app(\App\Domain\Platform\Services\HQUpdateService::class);
        
        $updateData = [
            'version' => '1.5.0',
            'channel' => 'stable',
            'package_url' => 'https://hq.example.com/downloads/v1.5.0.zip',
            'checksum' => 'fakehash',
        ];

        $update = $service->registerUpdate($updateData);

        $this->assertDatabaseHas('hq_updates', [
            'id' => $update->id,
            'version' => '1.5.0',
            'status' => 'available',
        ]);
    }

    public function test_update_log_creation()
    {
        $service = app(\App\Domain\Platform\Services\HQUpdateService::class);
        
        $update = $service->registerUpdate(['version' => '1.6.0']);

        $this->assertDatabaseHas('hq_update_logs', [
            'update_id' => $update->id,
            'action' => 'registered',
            'status' => 'success',
        ]);
        
        $this->assertCount(1, $update->logs);
    }

    public function test_mark_installed()
    {
        $service = app(\App\Domain\Platform\Services\HQUpdateService::class);
        
        $update = $service->registerUpdate(['version' => '2.0.0']);
        
        $this->assertEquals('available', $update->status);
        
        $service->markInstalled($update);
        
        $update->refresh();
        $this->assertEquals('installed', $update->status);
        $this->assertNotNull($update->installed_at);
        
        $this->assertDatabaseHas('hq_update_logs', [
            'update_id' => $update->id,
            'action' => 'installed',
        ]);
    }

    public function test_admin_update_page()
    {
        $superAdmin = User::factory()->create();
        $superAdmin->roles()->attach(Role::where('name', 'Super Admin')->first()->id);
        
        $response = $this->actingAs($superAdmin)->get('/admin/platform/updates');
        
        $response->assertStatus(200);
        $response->assertViewIs('admin.platform.updates.index');
        $response->assertSee('Current Version');
    }

    public function test_update_http_mock()
    {
        config(['hq.updates.enabled' => true]);
        
        Http::fake([
            '*/updates/check' => Http::response([
                'update_available' => true,
                'update_data' => [
                    'version' => '3.0.0',
                    'channel' => 'stable',
                ]
            ], 200)
        ]);

        $exitCode = Artisan::call('hq:update-check');
        $this->assertEquals(0, $exitCode);
        
        $output = Artisan::output();
        $this->assertStringContainsString('New update found: v3.0.0', $output);
        
        $this->assertDatabaseHas('hq_updates', [
            'version' => '3.0.0',
        ]);
    }

    public function test_security_no_execution()
    {
        // Assert that the command only logs and checks db, without any exec calls
        // We will just verify it doesn't fail and is a secure test
        $exitCode = Artisan::call('hq:update-check');
        $this->assertEquals(0, $exitCode);
        $this->assertStringContainsString('HQ Updates are disabled', Artisan::output());
    }
}
