<?php

namespace Tests\Feature;

use App\Models\AcademicTerm;
use App\Models\Branch;
use App\Models\Role;
use App\Models\Student;
use App\Models\StudentGuardian;
use App\Models\SystemIdentity;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentRegistrationUxTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected $branch;

    protected function setUp(): void
    {
        parent::setUp();

        SystemIdentity::firstOrCreate(['company_name' => 'Test'], ['product_name' => 'Test ERP']);
        AcademicTerm::firstOrCreate(['name' => '2025-2026'], ['start_date' => now(), 'end_date' => now()->addYear(), 'is_active' => true]);

        $this->branch = Branch::create(['name' => 'Kadıköy Şube', 'slug' => 'kadikoy-' . uniqid()]);
        
        $roleAdmin = Role::firstOrCreate(['name' => 'Super Admin'], ['guard_name' => 'web']);
        $this->adminUser = User::factory()->create(['branch_id' => $this->branch->id]);
        $this->adminUser->roles()->attach($roleAdmin);
    }

    public function test_student_can_be_created_without_user_account_by_default(): void
    {
        $response = $this->actingAs($this->adminUser)->post(route('admin.students.store'), [
            'student_number' => 'OGR-1001',
            'first_name' => 'Ahmet',
            'last_name' => 'Yılmaz',
            'gender' => 'Erkek',
            'status' => 'Active',
            'create_user_account' => '0',
            'create_parent_account' => '0',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $student = Student::where('student_number', 'OGR-1001')->firstOrFail();
        $this->assertNull($student->user_id);
        $this->assertEquals('Ahmet', $student->first_name);
        $this->assertEquals('Erkek', $student->gender);
    }

    public function test_student_can_be_created_with_user_account_when_toggle_enabled(): void
    {
        $response = $this->actingAs($this->adminUser)->post(route('admin.students.store'), [
            'student_number' => 'OGR-1002',
            'first_name' => 'Zeynep',
            'last_name' => 'Demir',
            'gender' => 'Kadın',
            'status' => 'Active',
            'create_user_account' => '1',
            'user_email' => 'zeynep.demir@test.com',
            'user_password' => 'password123',
            'user_password_confirmation' => 'password123',
            'create_parent_account' => '0',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $student = Student::where('student_number', 'OGR-1002')->firstOrFail();
        $this->assertNotNull($student->user_id);

        $user = User::find($student->user_id);
        $this->assertEquals('zeynep.demir@test.com', $user->email);
        $this->assertEquals('Zeynep Demir', $user->name);
    }

    public function test_student_and_parent_user_accounts_created_together_when_both_toggles_enabled(): void
    {
        $response = $this->actingAs($this->adminUser)->post(route('admin.students.store'), [
            'student_number' => 'OGR-1003',
            'first_name' => 'Can',
            'last_name' => 'Kaya',
            'gender' => 'Erkek',
            'status' => 'Active',
            'create_user_account' => '1',
            'user_email' => 'can.kaya@test.com',
            'user_password' => 'password123',
            'user_password_confirmation' => 'password123',
            'create_parent_account' => '1',
            'guardian_name' => 'Mehmet Kaya',
            'guardian_relation' => 'Baba',
            'guardian_phone' => '+90 (555) 123 45 67',
            'guardian_email' => 'mehmet.kaya@test.com',
            'guardian_password' => 'password123',
            'guardian_password_confirmation' => 'password123',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $student = Student::where('student_number', 'OGR-1003')->firstOrFail();
        $this->assertNotNull($student->user_id);

        $guardian = StudentGuardian::where('student_id', $student->id)->firstOrFail();
        $this->assertEquals('Mehmet Kaya', $guardian->guardian_name);
        $this->assertNotNull($guardian->user_id);

        $parentUser = User::find($guardian->user_id);
        $this->assertEquals('mehmet.kaya@test.com', $parentUser->email);
    }

    public function test_gender_validation_requires_kadin_or_erkek(): void
    {
        $response = $this->actingAs($this->adminUser)->post(route('admin.students.store'), [
            'student_number' => 'OGR-1004',
            'first_name' => 'Elif',
            'last_name' => 'Şahin',
            'gender' => 'Geçersiz',
        ]);

        $response->assertSessionHasErrors(['gender']);
    }

    public function test_parent_detail_page_renders_linked_students(): void
    {
        $student = Student::create([
            'branch_id' => $this->branch->id,
            'student_number' => 'OGR-1005',
            'first_name' => 'Murat',
            'last_name' => 'Candan',
            'gender' => 'Erkek',
            'status' => 'Active',
        ]);

        $guardian = StudentGuardian::create([
            'student_id' => $student->id,
            'guardian_name' => 'Hasan Candan',
            'relation' => 'Baba',
            'phone' => '5559876543',
            'email' => 'hasan.candan@test.com',
        ]);

        $response = $this->actingAs($this->adminUser)->get(route('admin.parents.show', $guardian->id));

        $response->assertOk();
        $response->assertSee('Hasan Candan');
        $response->assertSee('Murat Candan');
        $response->assertSee('OGR-1005');
    }
}
