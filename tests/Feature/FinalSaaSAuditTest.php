<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\Student;

class FinalSaaSAuditTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->branch = \App\Models\Branch::factory()->create([
            'name' => 'Test Branch',
            'slug' => 'test-branch'
        ]);

        \App\Models\SystemIdentity::create([
            'uuid' => (string) \Illuminate\Support\Str::uuid(),
            'company_name' => 'Test',
            'brand_name' => 'Test',
        ]);

        \App\Models\AcademicTerm::create([
            'name' => 'Test Term',
            'start_date' => now()->subDay(),
            'end_date' => now()->addYear(),
            'is_active' => true,
        ]);
        // Seed basic roles
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);
    }

    public function test_unauthenticated_users_cannot_access_dashboard()
    {
        // 1. Auth Flow Test
        $response = $this->get('/admin/reporting/dashboard');
        $response->assertRedirect('/login');
    }

    public function test_user_without_roles_cannot_access_admin_dashboard()
    {
        // 2. Role-Based Auth Test
        $user = User::factory()->create();
        // User has no role, so 'auth' passes, but 'permission:dashboard.view' fails
        $response = $this->actingAs($user)->get('/admin/reporting/dashboard');
        
        // It should either be 403 or redirect
        $response->assertStatus(403);
    }

    public function test_finance_invoice_page_loads_for_authorized_user()
    {
        // 3. Finance / Invoicing Test
        $user = User::factory()->create();
        $role = Role::where('name', 'Super Admin')->first();
        if ($role) {
            $user->roles()->attach($role);
        }

        $response = $this->actingAs($user)->get('/admin/invoices/dashboard');
        $response->assertStatus(200);
    }

    public function test_student_portal_access()
    {
        // 4. Student Portal Access
        $student = User::factory()->create();
        // Assuming student role exists
        $studentRole = Role::where('name', 'Öğrenci')->first();
        if ($studentRole) {
            $student->roles()->attach($studentRole);
        }

        // Student tries to access admin dashboard -> 403
        $response = $this->actingAs($student)->get('/admin/reporting/dashboard');
        $response->assertStatus(403);
    }

    public function test_admin_can_access_student_list_without_errors()
    {
        // 5. Admin Dashboard Access (Students list test)
        $admin = User::factory()->create();
        $role = Role::where('name', 'Super Admin')->first();
        if ($role) {
            $admin->roles()->attach($role);
        }

        // Just access the empty list
        $response = $this->actingAs($admin)->get('/admin/students');
        $response->assertStatus(200);
    }
}
