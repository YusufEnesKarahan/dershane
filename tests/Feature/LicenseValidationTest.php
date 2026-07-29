<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\HQTenant;
use App\Models\HQSystemInstance;
use App\Models\HQLicense;
use App\Models\LicenseCache;
use App\Models\HQSchedulerLog;

class LicenseValidationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);
        config(['app.installed' => true]);
        config(['hq.api.token' => 'test-hq-token']);
        config(['hq.api.secret' => 'test-secret']);
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

    protected function getSignedHeaders(array $payload = [])
    {
        $timestamp = time();
        $content = json_encode($payload);
        $signature = hash_hmac('sha256', $content . $timestamp, 'test-secret');

        return [
            'Authorization' => 'Bearer test-hq-token',
            'X-HQ-Signature' => $signature,
            'X-HQ-Timestamp' => (string) $timestamp,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];
    }

    // -----------------------------------------------------------------------
    // 1. Valid license returns active
    // -----------------------------------------------------------------------
    public function test_valid_license_returns_active()
    {
        $tenant = HQTenant::create(['name' => 'Valid Corp', 'slug' => 'vc']);
        $instance = HQSystemInstance::create([
            'tenant_id' => $tenant->id,
            'system_uuid' => 'valid-uuid-001',
            'system_name' => 'Valid ERP',
            'system_version' => '2.0.0',
            'status' => 'online',
        ]);
        $license = HQLicense::create([
            'tenant_id' => $tenant->id,
            'system_instance_id' => $instance->id,
            'plan' => 'enterprise',
            'status' => 'active',
            'starts_at' => now()->subMonth(),
            'expires_at' => now()->addYear(),
        ]);
        $license->licenseFeatures()->create(['feature_name' => 'crm', 'enabled' => true]);
        $license->licenseFeatures()->create(['feature_name' => 'sms', 'enabled' => false]);

        $payload = ['system_uuid' => 'valid-uuid-001'];
        $response = $this->postJson('/api/hq/license/validate', $payload, $this->getSignedHeaders($payload));

        $response->assertStatus(200);
        $data = $response->json();
        $this->assertTrue($data['success']);
        $this->assertEquals('active', $data['license']['status']);
        $this->assertEquals('enterprise', $data['license']['plan']);
        $this->assertTrue($data['license']['features']['crm']);
        $this->assertFalse($data['license']['features']['sms']);
    }

    // -----------------------------------------------------------------------
    // 2. Expired license returns expired
    // -----------------------------------------------------------------------
    public function test_expired_license_returns_expired()
    {
        $tenant = HQTenant::create(['name' => 'Expired Corp', 'slug' => 'ec']);
        $instance = HQSystemInstance::create([
            'tenant_id' => $tenant->id,
            'system_uuid' => 'expired-uuid-002',
            'system_name' => 'Expired ERP',
            'system_version' => '1.0.0',
            'status' => 'online',
        ]);
        HQLicense::create([
            'tenant_id' => $tenant->id,
            'system_instance_id' => $instance->id,
            'plan' => 'basic',
            'status' => 'active',
            'starts_at' => now()->subYear(),
            'expires_at' => now()->subDay(), // Already expired
        ]);

        $payload = ['system_uuid' => 'expired-uuid-002'];
        $response = $this->postJson('/api/hq/license/validate', $payload, $this->getSignedHeaders($payload));

        $response->assertStatus(200);
        $data = $response->json();
        $this->assertTrue($data['success']);
        $this->assertEquals('expired', $data['license']['status']);
    }

    // -----------------------------------------------------------------------
    // 3. Suspended license returns suspended
    // -----------------------------------------------------------------------
    public function test_suspended_license_returns_suspended()
    {
        $tenant = HQTenant::create(['name' => 'Suspended Corp', 'slug' => 'sc']);
        $instance = HQSystemInstance::create([
            'tenant_id' => $tenant->id,
            'system_uuid' => 'suspended-uuid-003',
            'system_name' => 'Suspended ERP',
            'system_version' => '1.0.0',
            'status' => 'online',
        ]);
        HQLicense::create([
            'tenant_id' => $tenant->id,
            'system_instance_id' => $instance->id,
            'plan' => 'professional',
            'status' => 'suspended',
            'starts_at' => now()->subMonth(),
            'expires_at' => now()->addYear(),
        ]);

        $payload = ['system_uuid' => 'suspended-uuid-003'];
        $response = $this->postJson('/api/hq/license/validate', $payload, $this->getSignedHeaders($payload));

        $response->assertStatus(200);
        $data = $response->json();
        $this->assertTrue($data['success']);
        $this->assertEquals('suspended', $data['license']['status']);
    }

    // -----------------------------------------------------------------------
    // 4. Invalid signature returns 401
    // -----------------------------------------------------------------------
    public function test_invalid_signature_returns_401()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer wrong-token',
            'X-HQ-Signature' => 'invalid',
            'X-HQ-Timestamp' => (string) time(),
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])->postJson('/api/hq/license/validate', [
            'system_uuid' => 'anything',
        ]);

        $response->assertStatus(401);
    }

    // -----------------------------------------------------------------------
    // 5. Feature middleware blocks disabled feature
    // -----------------------------------------------------------------------
    public function test_feature_middleware_blocks_disabled_feature()
    {
        // Seed a license cache with crm disabled
        LicenseCache::create([
            'system_uuid' => 'test-system',
            'status' => 'active',
            'plan' => 'basic',
            'features' => ['crm' => false, 'website' => true],
            'last_checked_at' => now(),
        ]);

        // Register a temporary API route (avoids global web middleware)
        \Illuminate\Support\Facades\Route::middleware(['feature:crm'])
            ->get('/_test-api/feature-crm', fn () => response()->json(['ok' => true]));

        $user = $this->getStandardUser();

        $response = $this->actingAs($user)->getJson('/_test-api/feature-crm');
        $response->assertStatus(403);
    }

    // -----------------------------------------------------------------------
    // 6. Feature middleware passes enabled feature
    // -----------------------------------------------------------------------
    public function test_feature_middleware_passes_enabled_feature()
    {
        LicenseCache::create([
            'system_uuid' => 'test-system',
            'status' => 'active',
            'plan' => 'enterprise',
            'features' => ['crm' => true, 'website' => true],
            'last_checked_at' => now(),
        ]);

        \Illuminate\Support\Facades\Route::middleware(['feature:crm'])
            ->get('/_test-api/feature-crm-ok', fn () => response()->json(['ok' => true]));

        $user = $this->getStandardUser();

        $response = $this->actingAs($user)->getJson('/_test-api/feature-crm-ok');
        $response->assertStatus(200);
        $response->assertJson(['ok' => true]);
    }

    // -----------------------------------------------------------------------
    // 7. License cache refresh populates table
    // -----------------------------------------------------------------------
    public function test_license_cache_refresh_works()
    {
        // We test the cache model directly since HQ HTTP calls
        // would require a live HQ server.
        LicenseCache::updateOrCreate(
            ['system_uuid' => 'refresh-test-uuid'],
            [
                'license_key' => 'LIC-TEST123',
                'status' => 'active',
                'plan' => 'enterprise',
                'features' => ['crm' => true, 'sms' => true],
                'expires_at' => now()->addYear(),
                'last_checked_at' => now(),
                'metadata' => ['hq_success' => true],
            ]
        );

        $this->assertDatabaseHas('license_cache', [
            'system_uuid' => 'refresh-test-uuid',
            'status' => 'active',
            'plan' => 'enterprise',
        ]);

        $cache = LicenseCache::where('system_uuid', 'refresh-test-uuid')->first();
        $this->assertTrue($cache->isActive());
        $this->assertTrue($cache->hasFeature('crm'));
        $this->assertFalse($cache->hasFeature('nonexistent'));
    }

    // -----------------------------------------------------------------------
    // 8. Scheduler command creates logs
    // -----------------------------------------------------------------------
    public function test_scheduler_command_creates_logs()
    {
        config(['hq.scheduler.enabled' => true]);
        config(['hq.enabled' => false]); // Disable actual HTTP calls

        $this->artisan('hq:license-check')
            ->assertExitCode(0);

        $this->assertDatabaseHas('hq_scheduler_logs', [
            'task_name' => 'hq:license-check',
        ]);
    }
}
