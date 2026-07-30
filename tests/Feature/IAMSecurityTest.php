<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\HQTenant;
use App\Models\HQRole;
use App\Models\HQPermission;
use App\Models\HQAccessPolicy;
use App\Models\HQApiKey;
use App\Models\HQServiceAccount;
use App\Models\HQSecuritySession;
use App\Domain\HQ\Services\IAM\HQPermissionService;
use App\Domain\HQ\Services\IAM\HQAccessPolicyService;
use App\Domain\HQ\Services\IAM\HQApiKeyService;
use App\Domain\HQ\Services\IAM\HQServiceAccountService;
use App\Domain\HQ\Services\IAM\SessionManagementService;
use App\Domain\HQ\Services\IAM\LoginSecurityService;
use App\Domain\HQ\Services\IAM\MfaService;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;

class IAMSecurityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Since we are mocking events, we shouldn't fake globally if we need them, 
        // but for IAM testing we can fake specific ones.
        Event::fake([
            \App\Events\RoleAssigned::class,
            \App\Events\PermissionChanged::class,
            \App\Events\ApiKeyRevoked::class,
            \App\Events\SuspiciousLoginDetected::class
        ]);
    }

    public function test_it_creates_models_and_migrations_successfully()
    {
        $tenant = HQTenant::create(['name' => 'Acme Corp', 'domain' => 'acme.com', 'slug' => 'acme', 'uuid' => 'u123']);
        $user = User::factory()->create();

        $role = HQRole::create([
            'tenant_id' => $tenant->id,
            'name' => 'Tenant Admin',
            'slug' => 'tenant-admin'
        ]);

        $permission = HQPermission::create([
            'name' => 'View Users',
            'slug' => 'users.view',
            'module' => 'users'
        ]);

        $this->assertDatabaseHas('hq_roles', ['slug' => 'tenant-admin']);
        $this->assertDatabaseHas('hq_permissions', ['slug' => 'users.view']);

        $role->permissions()->attach($permission->id);
        $role->users()->attach($user->id);

        $this->assertDatabaseHas('hq_user_roles', ['user_id' => $user->id, 'role_id' => $role->id]);
        $this->assertCount(1, $role->permissions);
    }

    public function test_permission_service_rbac_logic()
    {
        $tenant = HQTenant::create(['name' => 'Acme Corp', 'domain' => 'acme.com', 'slug' => 'acme', 'uuid' => 'u123']);
        $user = User::factory()->create();
        
        $role = HQRole::create(['tenant_id' => $tenant->id, 'name' => 'Manager', 'slug' => 'manager']);
        $permission = HQPermission::create(['name' => 'View Billing', 'slug' => 'billing.view', 'module' => 'billing']);
        
        $service = app(HQPermissionService::class);
        
        // Assert false before assign
        $this->assertFalse($service->hasPermission($user, 'billing.view'));
        $this->assertFalse($service->hasRole($user, 'manager'));

        // Assign role
        $service->assignRole($user, $role);
        Event::assertDispatched(\App\Events\RoleAssigned::class);
        $this->assertTrue($service->hasRole($user, 'manager'));

        // Sync permissions
        $service->syncPermissions($role, [$permission->id]);
        Event::assertDispatched(\App\Events\PermissionChanged::class);

        // Assert true after assign
        $this->assertTrue($service->hasPermission($user, 'billing.view'));

        // Remove role
        $service->removeRole($user, $role);
        $this->assertFalse($service->hasRole($user, 'manager'));
        
        // Test Super Admin bypass
        $superAdminRole = HQRole::create(['name' => 'Super Admin', 'slug' => 'super-admin']);
        $service->assignRole($user, $superAdminRole);
        
        $this->assertTrue($service->hasPermission($user, 'literally.anything'));
    }

    public function test_access_policy_service_abac_logic()
    {
        $tenant = HQTenant::create(['name' => 'Acme Corp', 'domain' => 'acme.com', 'slug' => 'acme', 'uuid' => 'u123']);
        $user = User::factory()->create();

        HQAccessPolicy::create([
            'tenant_id' => $tenant->id,
            'name' => 'Strict IP Policy',
            'ip_restrictions' => ['192.168.1.100'],
            'is_active' => true
        ]);

        $service = app(HQAccessPolicyService::class);

        request()->server->set('REMOTE_ADDR', '10.0.0.1');
        $this->assertFalse($service->evaluatePolicy($user, $tenant->id, 'billing'));

        // Change IP to allowed
        request()->server->set('REMOTE_ADDR', '192.168.1.100');
        $this->assertTrue($service->evaluatePolicy($user, $tenant->id, 'billing'));
    }

    public function test_api_key_service_lifecycle()
    {
        $tenant = HQTenant::create(['name' => 'Acme Corp', 'domain' => 'acme.com', 'slug' => 'acme', 'uuid' => 'u123']);
        $user = User::factory()->create();

        $service = app(HQApiKeyService::class);
        $plainToken = $service->generateApiKey($user, $tenant->id, 'Dev Key');

        $this->assertDatabaseHas('hq_api_keys', [
            'name' => 'Dev Key',
            'tenant_id' => $tenant->id,
            'is_revoked' => false,
            'usage_count' => 0
        ]);

        $this->assertNotNull($plainToken);

        $validatedKey = $service->validateKey($plainToken);
        $this->assertNotNull($validatedKey);
        $this->assertEquals(1, $validatedKey->usage_count);

        $service->revokeApiKey($validatedKey);
        Event::assertDispatched(\App\Events\ApiKeyRevoked::class);

        $this->assertNull($service->validateKey($plainToken));
    }

    public function test_service_account_lifecycle()
    {
        $tenant = HQTenant::create(['name' => 'Acme Corp', 'domain' => 'acme.com', 'slug' => 'acme', 'uuid' => 'u123']);

        $service = app(HQServiceAccountService::class);
        $result = $service->createServiceAccount($tenant->id, 'Backup Agent');

        $this->assertDatabaseHas('hq_service_accounts', [
            'name' => 'Backup Agent',
            'tenant_id' => $tenant->id,
            'is_active' => true
        ]);

        $this->assertNotNull($result['plain_token']);
        
        $validated = $service->validateToken($result['plain_token']);
        $this->assertNotNull($validated);
        $this->assertEquals('Backup Agent', $validated->name);

        $service->disableServiceAccount($validated);
        $this->assertNull($service->validateToken($result['plain_token']));
    }

    public function test_mfa_service_logic()
    {
        $tenant = HQTenant::create(['name' => 'Acme Corp', 'domain' => 'acme.com', 'slug' => 'acme', 'uuid' => 'u123']);
        $user = User::factory()->create();

        $service = app(MfaService::class);
        $mfaData = $service->enableMfa($user);

        $this->assertDatabaseHas('hq_mfa_settings', [
            'user_id' => $user->id,
            'is_enabled' => true
        ]);

        $this->assertNotNull($mfaData['secret']);
        $this->assertCount(8, $mfaData['recovery_codes']);

        // Mock verification
        $this->assertTrue($service->verifyTotp($user, '123456'));
        $this->assertFalse($service->verifyTotp($user, '000000'));

        $service->disableMfa($user);
        $this->assertDatabaseHas('hq_mfa_settings', [
            'user_id' => $user->id,
            'is_enabled' => false
        ]);
        $this->assertFalse($service->verifyTotp($user, '123456'));
    }

    public function test_login_security_service()
    {
        $tenant = HQTenant::create(['name' => 'Acme Corp', 'domain' => 'acme.com', 'slug' => 'acme', 'uuid' => 'u123']);
        $user = User::factory()->create();

        $service = app(LoginSecurityService::class);
        
        // Simulate failures
        for ($i=0; $i<5; $i++) {
            $service->recordLoginAttempt($user, false);
        }

        $this->assertDatabaseCount('hq_login_attempts', 5);
        Event::assertDispatched(\App\Events\SuspiciousLoginDetected::class);

        // Simulate success
        $service->recordLoginAttempt($user, true);
        $this->assertDatabaseHas('hq_login_attempts', [
            'user_id' => $user->id,
            'is_successful' => true
        ]);
    }

    public function test_session_management_service()
    {
        $tenant = HQTenant::create(['name' => 'Acme Corp', 'domain' => 'acme.com', 'slug' => 'acme', 'uuid' => 'u123']);
        $user = User::factory()->create();

        $service = app(SessionManagementService::class);
        $token = $service->createSession($user);

        $this->assertDatabaseHas('hq_security_sessions', [
            'user_id' => $user->id,
            'is_active' => true
        ]);

        $session = $service->validateAndUpdateSession($token);
        $this->assertNotNull($session);
        $this->assertEquals($user->id, $session->user_id);

        $activeSessions = $service->getActiveSessions($user);
        $this->assertCount(1, $activeSessions);

        $service->terminateSession($session);
        $this->assertNull($service->validateAndUpdateSession($token));

        $service->createSession($user);
        $service->forceLogoutUser($user);
        $this->assertCount(0, $service->getActiveSessions($user));
    }
}
