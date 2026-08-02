<?php

namespace Tests\Feature;

use App\Domain\Auth\Dictionaries\PermissionDictionary;
use App\Models\Branch;
use App\Models\Classroom;
use App\Models\HQSchedulerLog;
use App\Models\License;
use App\Models\Plan;
use App\Models\PlatformAuditLog;
use App\Models\Role;
use App\Models\Student;
use App\Models\SystemIdentity;
use App\Models\Teacher;
use App\Models\User;
use App\Models\AcademicTerm;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SystemHealthTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdminUser;
    protected User $normalUserAccount;
    protected Branch $tenantBranch;

    protected function setUp(): void
    {
        parent::setUp();

        SystemIdentity::create(['company_name' => 'Test', 'product_name' => 'Test ERP']);
        AcademicTerm::create(['name' => '2025-2026', 'start_date' => now(), 'end_date' => now()->addYear(), 'is_active' => true]);

        $this->tenantBranch = Branch::create(['name' => 'Test Tenant', 'slug' => 'test-tenant']);

        $superRole = Role::firstOrCreate(['name' => 'Super Admin'], ['guard_name' => 'web']);
        $this->superAdminUser = User::factory()->create([
            'branch_id' => $this->tenantBranch->id,
            'last_login_at' => now()->subHours(2),
        ]);
        $this->superAdminUser->roles()->attach($superRole);

        $this->normalUserAccount = User::factory()->create(['branch_id' => $this->tenantBranch->id]);

        Plan::create(['name' => 'Pro', 'slug' => 'pro', 'price' => 500, 'is_active' => true]);
        License::create([
            'license_key' => 'TEST-LICENSE-' . uniqid(),
            'status' => 'active',
            'plan' => 'pro',
        ]);

        User::factory()->create([
            'branch_id' => $this->tenantBranch->id,
            'name' => 'Tenant Login User',
            'last_login_at' => now()->subHour(),
        ]);
        Student::create([
            'student_number' => 'S-1001',
            'first_name' => 'Ada',
            'last_name' => 'Yılmaz',
            'branch_id' => $this->tenantBranch->id,
            'status' => 'active',
        ]);
        Teacher::create([
            'user_id' => $this->superAdminUser->id,
            'branch_id' => $this->tenantBranch->id,
            'title' => 'Matematik Öğretmeni',
            'status' => 'active',
        ]);
        Classroom::create([
            'code' => 'A1',
            'name' => 'A Sınıfı',
            'branch_id' => $this->tenantBranch->id,
            'capacity' => 24,
            'is_active' => true,
        ]);

        HQSchedulerLog::create([
            'task_name' => 'hq:telemetry',
            'status' => 'success',
            'started_at' => now()->subMinutes(20),
            'finished_at' => now()->subMinutes(19),
            'duration_ms' => 900,
            'result' => ['ok' => true],
        ]);
    }

    public function test_super_admin_can_view_system_health_dashboard(): void
    {
        $response = $this->actingAs($this->superAdminUser)->get(route('admin.saas.system-health.index'));

        $response->assertOk();
        $response->assertSee('Sistem Sağlığı');
        $response->assertSee('Laravel Versiyonu');
    }

    public function test_normal_user_cannot_access_system_health_dashboard(): void
    {
        $response = $this->actingAs($this->normalUserAccount)->get(route('admin.saas.system-health.index'));

        $response->assertStatus(403);
    }

    public function test_tenant_statistics_are_calculated_correctly(): void
    {
        $response = $this->actingAs($this->superAdminUser)->get(route('admin.saas.tenants.show', $this->tenantBranch->id));

        $response->assertOk();
        $response->assertSee('Kullanıcılar');
        $response->assertSee('Öğrenciler');
        $response->assertSee('Öğretmenler');
        $response->assertSee('Sınıflar');
        $response->assertSee('Tenant Login User');
    }

    public function test_audit_logs_are_created_for_tenant_suspend_and_activate(): void
    {
        $this->actingAs($this->superAdminUser)->post(route('admin.saas.tenants.suspend', $this->tenantBranch->id));
        $this->actingAs($this->superAdminUser)->post(route('admin.saas.tenants.activate', $this->tenantBranch->id));

        $this->assertDatabaseCount('platform_audit_logs', 2);

        $this->assertDatabaseHas('platform_audit_logs', [
            'action' => 'tenant.suspended',
            'target_type' => Branch::class,
            'target_id' => $this->tenantBranch->id,
        ]);

        $this->assertDatabaseHas('platform_audit_logs', [
            'action' => 'tenant.activated',
            'target_type' => Branch::class,
            'target_id' => $this->tenantBranch->id,
        ]);
    }
}