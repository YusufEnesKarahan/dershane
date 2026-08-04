<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Branch;
use App\Models\Student;
use App\Models\StudentGuardian;
use App\Models\AcademicTerm;
use App\Models\PaymentPlan;
use App\Models\Installment;
use App\Models\Role;
use App\Models\Permission;

class FinanceManagementTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $adminUser;
    protected $studentUser;
    protected $parentUser;
    protected $branch;
    protected $term;
    protected $student;
    protected $guardian;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->withoutMiddleware(\App\Http\Middleware\CheckOnboardingStatus::class);
        
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);

        $this->branch = Branch::create([
            'name' => 'Test Branch',
            'slug' => 'test-branch',
            'email' => 'test@branch.com',
            'phone' => '123456789',
            'address' => 'Test Address',
            'is_active' => true,
        ]);

        $this->term = AcademicTerm::create([
            'branch_id' => $this->branch->id,
            'name' => '2026-2027',
            'start_date' => now(),
            'end_date' => now()->addMonths(10),
        ]);

        $this->adminUser = User::factory()->create(['branch_id' => $this->branch->id]);
        $adminRole = Role::firstOrCreate(['name' => 'Admin']);
        $this->adminUser->roles()->attach($adminRole);

        $this->studentUser = User::factory()->create(['branch_id' => $this->branch->id]);
        $studentRole = Role::firstOrCreate(['name' => 'Student']);
        $this->studentUser->roles()->attach($studentRole);

        $this->student = Student::create([
            'branch_id' => $this->branch->id,
            'user_id' => $this->studentUser->id,
            'student_number' => 'STU-100',
            'first_name' => 'Test',
            'last_name' => 'Student'
        ]);

        $this->parentUser = User::factory()->create(['branch_id' => $this->branch->id]);
        $parentRole = Role::firstOrCreate(['name' => 'Parent']);
        $this->parentUser->roles()->attach($parentRole);

        $this->guardian = clone $this->student;
        StudentGuardian::create([
            'branch_id' => $this->branch->id,
            'user_id' => $this->parentUser->id,
            'student_id' => $this->student->id,
            'guardian_name' => 'Test Parent',
            'phone' => '987654321',
            'relation' => 'Father'
        ]);
    }

    public function test_admin_can_create_payment_plan_and_installments()
    {
        $this->actingAs($this->adminUser);

        $response = $this->post(route('admin.finance.store'), [
            'student_id' => $this->student->id,
            'academic_term_id' => $this->term->id,
            'title' => 'Test Plan',
            'total_amount' => 10000,
            'discount_amount' => 1000,
            'installment_count' => 10
        ]);

        $response->assertRedirect(route('admin.finance.index'));
        
        $this->assertDatabaseHas('payment_plans', [
            'title' => 'Test Plan',
            'net_amount' => 9000
        ]);

        $plan = PaymentPlan::where('title', 'Test Plan')->first();
        $this->assertCount(10, $plan->installments);
        
        $firstInstallment = $plan->installments()->first();
        $this->assertEquals(900, $firstInstallment->amount);
    }

    public function test_admin_can_collect_payment()
    {
        $this->actingAs($this->adminUser);

        $plan = app(\App\Domain\Finance\Services\FinanceManagementService::class)->createPaymentPlan([
            'branch_id' => $this->branch->id,
            'student_id' => $this->student->id,
            'academic_term_id' => $this->term->id,
            'title' => 'Test',
            'total_amount' => 5000,
            'discount_amount' => 0,
        ]);
        
        app(\App\Domain\Finance\Services\FinanceManagementService::class)->generateInstallments($plan, 5);

        $installment = $plan->installments()->first();

        $response = $this->post(route('admin.installments.collect', $installment), [
            'amount' => 500, // partial
            'payment_method' => 'cash'
        ]);

        $response->assertSessionHas('success');
        
        $installment->refresh();
        $this->assertEquals(500, $installment->paid_amount);
        $this->assertEquals(500, $installment->remaining_amount);
        $this->assertEquals('partial', $installment->status);
        
        $this->assertDatabaseHas('payments', [
            'amount' => 500,
            'installment_id' => $installment->id
        ]);
    }

    public function test_student_can_view_own_finance()
    {
        $this->actingAs($this->studentUser);
        
        $response = $this->get(route('student.finance.index'));
        $response->assertStatus(200);
        $response->assertViewHas('summary');
    }

    public function test_parent_can_view_child_finance()
    {
        $this->actingAs($this->parentUser);
        
        $response = $this->get(route('parent.finance.index'));
        $response->assertStatus(200);
        $response->assertViewHas('students');
    }

    public function test_tenant_isolation_in_finance()
    {
        $branch2 = Branch::create([
            'name' => 'Branch 2',
            'slug' => 'branch-2',
            'email' => 'test2@branch.com',
            'phone' => '123',
            'address' => 'Test 2',
            'is_active' => true,
        ]);
        $admin2 = User::factory()->create(['branch_id' => $branch2->id]);
        $adminRole2 = Role::firstOrCreate(['name' => 'Admin']);
        $admin2->roles()->attach($adminRole2);
        
        $plan = app(\App\Domain\Finance\Services\FinanceManagementService::class)->createPaymentPlan([
            'branch_id' => $this->branch->id,
            'student_id' => $this->student->id,
            'academic_term_id' => $this->term->id,
            'title' => 'Test',
            'total_amount' => 5000,
        ]);

        $this->actingAs($admin2);
        
        // Admin of branch 2 cannot view plan of branch 1
        $response = $this->get(route('admin.finance.show', $plan));
        $response->assertStatus(403);
    }
}
