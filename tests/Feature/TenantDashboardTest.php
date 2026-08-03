<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\Branch;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Plan;
use App\Models\Subscription;
use App\Enums\UserStatus;

class TenantDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->artisan('db:seed', ['--class' => 'RBACSeeder']);

        $this->tenantA = Branch::factory()->create(['name' => 'Tenant A', 'slug' => 'tenant-a']);
        $this->tenantB = Branch::factory()->create(['name' => 'Tenant B', 'slug' => 'tenant-b']);

        // Roles
        $tenantAdminRole = Role::where('name', 'tenant_admin')->first();
        $teacherRole = Role::where('name', 'teacher')->first();

        // Users
        $this->tenantAdminA = User::factory()->create(['branch_id' => $this->tenantA->id, 'status' => UserStatus::ACTIVE]);
        $this->tenantAdminA->roles()->attach($tenantAdminRole->id);

        $this->tenantAdminB = User::factory()->create(['branch_id' => $this->tenantB->id, 'status' => UserStatus::ACTIVE]);
        $this->tenantAdminB->roles()->attach($tenantAdminRole->id);

        $this->teacherA = User::factory()->create(['branch_id' => $this->tenantA->id, 'status' => UserStatus::ACTIVE]);
        $this->teacherA->roles()->attach($teacherRole->id);

        // Subscription
        $plan = Plan::create([
            'name' => 'Pro Plan',
            'slug' => 'pro-plan',
            'code' => 'PRO_PLAN',
            'price_monthly' => 500,
            'max_students' => 100,
            'max_teachers' => 20,
            'max_storage_gb' => 10,
        ]);
        
        $license = \App\Models\License::create([
            'branch_id' => $this->tenantA->id,
            'license_key' => 'TEST-KEY',
            'status' => 'active',
            'expires_at' => now()->addDays(30),
            'max_students' => 100,
            'max_teachers' => 20,
        ]);

        Subscription::create([
            'branch_id' => $this->tenantA->id,
            'plan_id' => $plan->id,
            'license_id' => $license->id,
            'status' => 'active',
            'starts_at' => now(),
            'ends_at' => now()->addDays(30),
        ]);

        // Data for Tenant A
        Student::create([
            'branch_id' => $this->tenantA->id,
            'user_id' => User::factory()->create()->id,
            'student_number' => 'STU-001',
            'first_name' => 'A1',
            'last_name' => 'Student',
            'email' => 'a1@test.com',
            'status' => 'active',
            'payment_type' => 'monthly',
        ]);
        
        Teacher::create([
            'branch_id' => $this->tenantA->id,
            'user_id' => $this->teacherA->id,
            'first_name' => 'T1',
            'last_name' => 'Teacher',
            'status' => 'active',
        ]);

        // Data for Tenant B
        Student::create([
            'branch_id' => $this->tenantB->id,
            'user_id' => User::factory()->create()->id,
            'student_number' => 'STU-002',
            'first_name' => 'B1',
            'last_name' => 'Student',
            'email' => 'b1@test.com',
            'status' => 'active',
            'payment_type' => 'monthly',
        ]);
    }

    public function test_tenant_admin_can_view_dashboard()
    {
        $response = $this->actingAs($this->tenantAdminA)->get(route('tenant.dashboard'));
        $response->assertStatus(200);
        $response->assertViewIs('dashboard.index');
        $response->assertSee('Dershane İşletme Özeti');
    }

    public function test_tenant_admin_dashboard_isolated_data()
    {
        // Tenant A has 1 student. Tenant B has 1 student.
        $response = $this->actingAs($this->tenantAdminA)->get(route('tenant.dashboard'));
        $response->assertStatus(200);
        
        $response->assertViewHas('statistics', function($stats) {
            return $stats['students'] === 1 && $stats['teachers'] === 1;
        });
    }

    public function test_subscription_limit_info_is_visible()
    {
        $response = $this->actingAs($this->tenantAdminA)->get(route('tenant.dashboard'));
        $response->assertStatus(200);
        $response->assertViewHas('limits', function($limits) {
            return $limits['students']['max'] === 100 && $limits['teachers']['max'] === 20;
        });
        
        // Ensure the plan name matches
        $response->assertViewHas('limits', function($limits) {
            return $limits['plan_name'] === 'Pro Plan';
        });
    }

    public function test_teacher_cannot_access_tenant_dashboard()
    {
        $response = $this->actingAs($this->teacherA)->get(route('tenant.dashboard'));
        $response->assertStatus(403); // Forbidden
    }
}
