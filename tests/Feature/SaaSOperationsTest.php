<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\License;
use App\Models\Plan;
use App\Models\User;
use App\Models\SystemIdentity;
use App\Models\AcademicTerm;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Role;
use Tests\TestCase;

class SaaSOperationsTest extends TestCase
{
    use RefreshDatabase;

    protected $superAdmin;
    protected $normalAdmin;
    protected $tenant;

    protected function setUp(): void
    {
        parent::setUp();
        
        SystemIdentity::create(['company_name' => 'Test', 'product_name' => 'Test ERP']);
        AcademicTerm::create(['name' => '2025-2026', 'start_date' => now(), 'end_date' => now()->addYear(), 'is_active' => true]);

        $this->tenant = Branch::create(['name' => 'Test Tenant', 'slug' => 'test-tenant']);
        
        $roleSuper = Role::firstOrCreate(['name' => 'Super Admin'], ['guard_name' => 'web']);
        $this->superAdmin = User::factory()->create(['branch_id' => $this->tenant->id]);
        $this->superAdmin->roles()->attach($roleSuper);
        
        $this->normalAdmin = User::factory()->create(['branch_id' => $this->tenant->id]);
        
        // Create system-wide license (no branch_id)
        $plan = Plan::create(['name' => 'Pro', 'slug' => 'pro', 'price' => 500, 'is_active' => true]);
        License::create([
            'license_key' => 'TEST-LICENSE-' . uniqid(),
            'status' => 'active',
            'plan_id' => $plan->id,
            'plan' => $plan->slug
        ]);
    }

    public function test_super_admin_can_access_saas_operations()
    {
        $response = $this->actingAs($this->superAdmin)->get(route('admin.saas.tenants.index'));
        $response->assertStatus(200);
        $response->assertSee('Test Tenant');
    }

    public function test_normal_admin_cannot_access_saas_operations()
    {
        $response = $this->actingAs($this->normalAdmin)->get(route('admin.saas.tenants.index'));
        $response->assertStatus(403);
    }

    public function test_super_admin_can_view_tenant_details()
    {
        $response = $this->actingAs($this->superAdmin)->get(route('admin.saas.tenants.show', $this->tenant->id));
        $response->assertStatus(200);
        $response->assertSee('Test Tenant');
        $response->assertSee('Genel Bilgiler');
    }

    public function test_super_admin_can_suspend_and_activate_license()
    {
        // Suspend
        $response = $this->actingAs($this->superAdmin)->post(route('admin.saas.tenants.suspend', $this->tenant->id));
        $response->assertRedirect();
        
        $this->assertEquals('suspended', License::first()->status);
        
        // Activate
        $response = $this->actingAs($this->superAdmin)->post(route('admin.saas.tenants.activate', $this->tenant->id));
        $response->assertRedirect();
        
        $this->assertEquals('active', License::first()->status);
    }
}
