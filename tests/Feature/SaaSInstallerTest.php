<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Branch;
use App\Models\License;
use App\Models\Role;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;

class SaaSInstallerTest extends TestCase
{
    use RefreshDatabase;

    protected string $lockFile;

    protected function setUp(): void
    {
        parent::setUp();
        $this->lockFile = storage_path('app/installed.lock');
        
        // Disable installed status for testing installation wizard
        config(['app.installed' => false]);

        // Ensure no lock file exists before test
        if (file_exists($this->lockFile)) {
            unlink($this->lockFile);
        }
    }

    protected function tearDown(): void
    {
        // Cleanup lock file
        if (file_exists($this->lockFile)) {
            unlink($this->lockFile);
        }
        parent::tearDown();
    }

    public function test_installation_page_accessible()
    {
        // App is NOT installed (no lock file)
        $response = $this->get('/install');
        
        $response->assertStatus(200);
        $response->assertViewIs('install.welcome');
    }

    public function test_middleware_redirects_uninstalled_visitor()
    {
        // App is NOT installed (no lock file)
        $response = $this->get('/'); // normal frontend/auth page
        
        $response->assertRedirect('/install');
    }

    public function test_installation_flag_works()
    {
        // App is NOT installed at first
        $this->assertFalse(file_exists($this->lockFile));

        // Create lock file manually to simulate installation completion
        file_put_contents($this->lockFile, 'lock');

        // Middleware should no longer redirect to /install
        $response = $this->get('/');
        
        $this->assertNotEquals(route('install.welcome'), $response->headers->get('Location'));
    }

    public function test_admin_created_and_installation_completes()
    {
        // 1. Run migrations/seeders via route to ensure roles exist
        $response = $this->post('/install/database/migrate');
        $response->assertRedirect('/install/admin');

        // Check that RolesAndPermissionsSeeder was run
        $this->assertTrue(Schema::hasTable('roles'));
        $this->assertTrue(Role::where('name', 'Super Admin')->exists());

        // 2. Submit form to create admin, default branch, default license
        $response = $this->post('/install/admin', [
            'branch_name' => 'Test Branch',
            'name' => 'SaaS Admin',
            'email' => 'saas@admin.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        // Put completed completed state in session to allow finish page check
        $response->assertRedirect('/install/finish');

        // Verify Branch was created
        $this->assertTrue(Branch::where('name', 'Test Branch')->exists());

        // Verify User was created and assigned Super Admin role
        $user = User::where('email', 'saas@admin.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals('Test Branch', $user->branch->name);
        $this->assertTrue($user->hasRole('Super Admin'));

        // Verify Trial License was created
        $this->assertTrue(License::where('status', 'trial')->where('plan', 'starter')->exists());

        // Verify Lock File exists
        $this->assertTrue(file_exists($this->lockFile));
    }

    public function test_cannot_reinstall()
    {
        // Simulate installed state
        file_put_contents($this->lockFile, 'lock');
        
        // Explicitly set app debug to false
        Config::set('app.debug', false);

        // Try to access installer welcome page
        $response = $this->get('/install');
        
        $response->assertStatus(403);
    }
}
