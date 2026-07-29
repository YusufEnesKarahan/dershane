<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\HQSystemInstance;
use App\Models\HQTenant;
use App\Models\HQAuditLog;
use App\Domain\HQ\Services\HQAuditService;
use App\Domain\HQ\Services\HQLicenseService;

class HQAuditTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // create a user
        $this->admin = User::factory()->create();
        $this->actingAs($this->admin);
    }

    public function test_audit_log_is_created_when_license_is_created()
    {
        $tenant = HQTenant::create([
            'name' => 'Test Tenant',
            'slug' => 'test-tenant',
            'status' => 'active',
            'contact_email' => 'test@example.com'
        ]);

        $licenseService = app(HQLicenseService::class);
        $licenseService->createLicense([
            'tenant_id' => $tenant->id,
            'plan' => 'enterprise',
            'features' => ['advanced_reporting' => true, 'api_access' => true],
        ]);

        $this->assertDatabaseHas('hq_audit_logs', [
            'action' => 'license.created',
            'category' => 'license',
            'tenant_id' => $tenant->id,
        ]);
    }

    public function test_audit_api_endpoint_records_log()
    {
        $tenant = HQTenant::create([
            'name' => 'Test Tenant API',
            'slug' => 'test-tenant-api',
            'status' => 'active',
            'contact_email' => 'api@example.com'
        ]);

        $instance = HQSystemInstance::create([
            'tenant_id' => $tenant->id,
            'system_uuid' => 'SYS-1234',
            'system_name' => 'Remote System',
            'system_version' => '1.0.0',
            'status' => 'online',
            'api_key' => 'secret123',
            'ip_address' => '127.0.0.1',
        ]);

        $data = [
            'system_id' => 'SYS-1234',
            'action' => 'remote.login.failed',
            'category' => 'security',
            'severity' => 'warning',
            'description' => 'Multiple failed logins detected on ERP.'
        ];

        $response = $this->withoutMiddleware(\App\Http\Middleware\VerifyHQApiSignature::class)
            ->postJson('/api/hq/audit/report', $data);

        $response->assertStatus(200);

        $this->assertDatabaseHas('hq_audit_logs', [
            'action' => 'remote.login.failed',
            'category' => 'security',
            'severity' => 'warning',
            'system_instance_id' => $instance->id,
        ]);
    }
}
