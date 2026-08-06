<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Branch;
use App\Models\Institution;
use App\Models\Plan;
use App\Models\Role;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Classroom;
use App\Models\Course;
use App\Models\Exam;
use App\Core\Context\TenantContext;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OnboardingFlowTest extends TestCase
{
    use RefreshDatabase;

    protected $planStarter;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware([
            \App\Http\Middleware\EnsureOnboardingCompleted::class,
            \App\Http\Middleware\CheckOnboardingStatus::class,
        ]);

        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);

        // Make sure Starter plan exists
        $this->planStarter = Plan::firstOrCreate(
            ['slug' => 'starter'],
            [
                'name' => 'Starter',
                'description' => 'Starter plan',
                'price' => 100,
                'is_active' => true,
                'max_students' => 200,
                'max_teachers' => 10,
                'max_classrooms' => 5,
            ]
        );
    }

    public function test_onboarding_wizard_provisioning_flow()
    {
        // 1. Submit Company step
        $response = $this->post(route('onboarding.company.store'), [
            'name' => 'Bilgi Dershanesi',
            'phone' => '5559998877',
            'email' => 'bilgi@dershane.com',
            'city' => 'Ankara',
        ]);
        $response->assertRedirect(route('onboarding.admin'));

        // 2. Submit Admin step
        $response = $this->post(route('onboarding.admin.store'), [
            'name' => 'Kemal Sunal',
            'email' => 'kemal@bilgi.com',
            'password' => 'secret123',
        ]);
        $response->assertRedirect(route('onboarding.branch'));

        // 3. Submit Branch step
        $response = $this->post(route('onboarding.branch.store'), [
            'branch_name' => 'Kızılay Merkez Şubesi',
            'address' => 'Kızılay, Ankara',
        ]);
        $response->assertRedirect(route('onboarding.completed'));

        // 5. Submit Completed step with demo data enabled
        $response = $this->post(route('onboarding.complete'), [
            'seed_demo' => '1',
        ]);
        $response->assertRedirect(route('admin.dashboard'));

        // Verify database records
        $institution = Institution::where('name', 'Bilgi Dershanesi')->first();
        $this->assertNotNull($institution);

        $branch = Branch::where('name', 'Kızılay Merkez Şubesi')->first();
        $this->assertNotNull($branch);

        $user = User::where('email', 'kemal@bilgi.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals($branch->id, $user->branch_id);
        $this->assertTrue($user->hasRole('Branch Admin'));

        // Verify license & subscription
        $this->assertNotNull($branch->subscription);
        $this->assertEquals($this->planStarter->id, $branch->subscription->plan_id);
        $this->assertNotNull($branch->subscription->license);

        // Verify seeded demo data
        TenantContext::setActiveBranchId($branch->id);
        $this->assertEquals(3, Classroom::count());
        $this->assertEquals(3, Teacher::count());
        $this->assertEquals(5, Course::count());
        $this->assertEquals(10, Student::count());
        $this->assertEquals(1, Exam::count());
        TenantContext::clear();
    }

    public function test_created_admin_cannot_access_other_tenants()
    {
        // 1. Create Tenant A & Admin A
        $branchA = Branch::create(['name' => 'Şube A', 'slug' => 'sube-a']);
        $adminA = User::factory()->create(['branch_id' => $branchA->id]);
        $role = Role::where('name', 'Branch Admin')->first();
        $adminA->roles()->sync([$role->id]);

        // 2. Create Tenant B & Admin B
        $branchB = Branch::create(['name' => 'Şube B', 'slug' => 'sube-b']);
        $adminB = User::factory()->create(['branch_id' => $branchB->id]);
        $adminB->roles()->sync([$role->id]);

        // 3. Act as Admin A and query branches/students under BranchScope
        TenantContext::setActiveBranchId($branchA->id);
        $this->assertEquals($branchA->id, TenantContext::getActiveBranchId());

        $studentsQuery = Student::all();
        $this->assertFalse($studentsQuery->contains('branch_id', $branchB->id));

        TenantContext::clear();
    }

    public function test_onboarding_requires_unique_company_and_branch_names()
    {
        // Create an existing institution and branch
        Institution::create(['name' => 'Existing Company', 'slug' => 'existing-company', 'uuid' => \Illuminate\Support\Str::uuid()]);
        Branch::create(['name' => 'Existing Branch', 'slug' => 'existing-branch']);

        // Try submitting company step with duplicate name
        $response = $this->post(route('onboarding.company.store'), [
            'name' => 'Existing Company',
            'phone' => '1234567890',
            'email' => 'test@company.com',
            'city' => 'Istanbul',
        ]);
        $response->assertSessionHasErrors(['name']);

        // Try submitting branch step with duplicate name
        $response = $this->post(route('onboarding.branch.store'), [
            'branch_name' => 'Existing Branch',
            'address' => 'Istanbul, Turkey',
        ]);
        $response->assertSessionHasErrors(['branch_name']);
    }
}
