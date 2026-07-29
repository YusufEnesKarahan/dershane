<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\HQConfigurationProfile;
use App\Models\HQConfigurationItem;
use App\Models\HQConfigurationVersion;
use App\Models\HQSystemInstance;
use App\Models\HQTenant;
use Illuminate\Support\Facades\Crypt;
use App\Domain\System\Commands\CommandRegistry;
use App\Domain\HQ\Enums\HQCommandType;

class HQConfigurationTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);
        $this->admin = User::factory()->create();
        $this->admin->roles()->attach(\App\Models\Role::where('name', 'Super Admin')->first()->id);

        config(['app.installed' => true]);
        config(['hq.api.token' => 'test-hq-token']);
        config(['hq.api.secret' => 'test-secret']);
    }

    public function test_can_create_configuration_profile()
    {
        $response = $this->actingAs($this->admin)->post(route('admin.platform.hq_central.configurations.store'), [
            'name' => 'Test Global Config',
            'scope' => 'global',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('hq_configuration_profiles', [
            'name' => 'Test Global Config',
            'scope' => 'global',
        ]);
        
        $this->assertDatabaseHas('hq_configuration_logs', [
            'action' => 'create_profile'
        ]);
    }

    public function test_can_add_configuration_item_with_encryption()
    {
        $profile = HQConfigurationProfile::create([
            'name' => 'Test Profile',
            'scope' => 'global',
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.platform.hq_central.configurations.items.store', $profile), [
            'key' => 'SMTP_PASSWORD',
            'value' => 'supersecret',
            'type' => 'encrypted',
            'is_sensitive' => true,
        ]);

        $response->assertRedirect();
        
        $item = HQConfigurationItem::where('key', 'SMTP_PASSWORD')->first();
        $this->assertNotNull($item);
        
        // Ensure raw value is not the plaintext
        $this->assertNotEquals('supersecret', $item->value);
        
        // Ensure decrypted value is correct
        $this->assertEquals('supersecret', $item->decrypted_value);
    }

    public function test_can_create_version_and_rollback()
    {
        $profile = HQConfigurationProfile::create([
            'name' => 'Test Profile',
            'scope' => 'global',
        ]);

        // Add item
        $item = HQConfigurationItem::create([
            'profile_id' => $profile->id,
            'key' => 'TEST_KEY',
            'value' => 'V1',
            'type' => 'string'
        ]);

        // Create version 1
        $this->actingAs($this->admin)->post(route('admin.platform.hq_central.configurations.version', $profile), [
            'notes' => 'Version 1'
        ]);

        $this->assertDatabaseHas('hq_configuration_versions', [
            'profile_id' => $profile->id,
            'version' => 1
        ]);

        // Modify item
        $item->update(['value' => 'V2']);

        // Create version 2
        $this->actingAs($this->admin)->post(route('admin.platform.hq_central.configurations.version', $profile), [
            'notes' => 'Version 2'
        ]);

        $this->assertDatabaseHas('hq_configuration_versions', [
            'profile_id' => $profile->id,
            'version' => 2
        ]);

        // Rollback to version 1
        $this->actingAs($this->admin)->post(route('admin.platform.hq_central.configurations.rollback', [$profile, 1]));

        // Value should be V1 again
        $restoredItem = HQConfigurationItem::where('key', 'TEST_KEY')->first();
        $this->assertEquals('V1', $restoredItem->value);

        // A new version 3 should have been created for the rollback
        $this->assertDatabaseHas('hq_configuration_versions', [
            'profile_id' => $profile->id,
            'version' => 3
        ]);
    }

    public function test_api_sync_returns_correct_profile_and_decrypts_values()
    {
        // Setup instances
        $tenant = HQTenant::create(['name' => 'Test Tenant', 'slug' => 'test-tenant']);
        $instance = HQSystemInstance::create([
            'tenant_id' => $tenant->id,
            'system_uuid' => 'sys-uuid-test',
            'system_name' => 'Sys1',
            'system_version' => '1.0.0',
            'status' => 'online'
        ]);

        $profile = HQConfigurationProfile::create([
            'name' => 'Instance Config',
            'scope' => 'instance',
            'system_instance_id' => $instance->id,
        ]);

        $item = new HQConfigurationItem();
        $item->profile_id = $profile->id;
        $item->key = 'API_KEY';
        $item->type = 'encrypted';
        $item->is_sensitive = true;
        // The mutator will encrypt it
        $item->value = 'secret-key-123';
        $item->save();

        // Sign the request
        $secret = 'test-secret';
        config(['hq.api.secret' => $secret]);
        $timestamp = time();
        $payload = [
            'system_uuid' => 'sys-uuid-test',
            'environment' => 'testing',
        ];
        $signature = hash_hmac('sha256', json_encode($payload) . $timestamp, $secret);

        $response = $this->postJson('/api/hq/configuration/sync', $payload, [
            'Authorization' => 'Bearer test-hq-token',
            'X-HQ-Signature' => $signature,
            'X-HQ-Timestamp' => (string) $timestamp,
        ]);

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'key' => 'API_KEY',
            'value' => 'secret-key-123', // the API sends it decrypted for the instance to use
        ]);
    }
}
