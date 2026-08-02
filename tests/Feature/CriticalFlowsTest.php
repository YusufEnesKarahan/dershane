<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Classroom;
use App\Models\Discount;
use App\Models\Scholarship;
use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;

class CriticalFlowsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setupSaaSTenant();
    }

    public function test_can_create_student_flow()
    {
        $response = $this->actingAs($this->superAdmin)->post(route('admin.students.store'), [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'student_number' => 'STU-100',
            'branch_id' => $this->branch->id,
            'status' => 'Active',
            'gender' => 'Male'
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect();
        
        $this->assertDatabaseHas('students', [
            'first_name' => 'John',
            'student_number' => 'STU-100',
            'branch_id' => $this->branch->id
        ]);
    }

    public function test_can_update_student_flow()
    {
        $studentId = DB::table('students')->insertGetId([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'student_number' => 'STU-100',
            'branch_id' => $this->branch->id,
            'status' => 'Active',
            'gender' => 'Male'
        ]);
        $student = Student::find($studentId);

        $response = $this->actingAs($this->superAdmin)->put(route('admin.students.update', $student->id), [
            'first_name' => 'Jane',
            'last_name' => 'Doe',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect();

        $this->assertDatabaseHas('students', [
            'id' => $student->id,
            'first_name' => 'Jane'
        ]);
    }

    public function test_can_delete_student_flow()
    {
        $studentId = DB::table('students')->insertGetId([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'student_number' => 'STU-101',
            'branch_id' => $this->branch->id,
            'status' => 'Active',
            'gender' => 'Male'
        ]);
        $student = Student::find($studentId);

        $response = $this->actingAs($this->superAdmin)->delete(route('admin.students.destroy', $student->id));

        $response->assertRedirect();
        
        // Ensure student is soft deleted or deleted
        $this->assertDatabaseMissing('students', [
            'id' => $student->id,
            'deleted_at' => null // Check if soft deleted
        ]);
    }

    public function test_can_create_teacher_flow()
    {
        $user = \App\Models\User::factory()->create(['branch_id' => $this->branch->id]);

        $response = $this->actingAs($this->superAdmin)->post(route('admin.teachers.store'), [
            'user_id' => $user->id,
            'branch_id' => $this->branch->id,
            'title' => 'Senior Math Teacher',
            'specialties' => 'Mathematics, Physics',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect();
        
        $this->assertDatabaseHas('teachers', [
            'user_id' => $user->id,
            'title' => 'Senior Math Teacher'
        ]);
    }

    public function test_can_create_classroom_flow()
    {
        $response = $this->actingAs($this->superAdmin)->post(route('admin.classrooms.store'), [
            'name' => 'Class A',
            'code' => 'CL-A',
            'capacity' => 20,
            'branch_id' => $this->branch->id,
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect();
        
        $this->assertDatabaseHas('classrooms', [
            'code' => 'CL-A',
            'branch_id' => $this->branch->id
        ]);
    }

    public function test_can_create_discount_flow()
    {
        $response = $this->actingAs($this->superAdmin)->post(route('admin.discounts.store'), [
            'name' => 'Early Bird',
            'code' => 'EB2026',
            'type' => 'percentage',
            'value' => 10,
            'is_active' => true
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect();

        $this->assertDatabaseHas('discounts', [
            'name' => 'Early Bird',
            'value' => 10
        ]);
    }

    public function test_can_create_scholarship_flow()
    {
        $studentId = DB::table('students')->insertGetId([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'student_number' => 'STU-102',
            'branch_id' => $this->branch->id,
            'status' => 'Active',
            'gender' => 'Male'
        ]);
        $student = Student::find($studentId);

        $response = $this->actingAs($this->superAdmin)->post(route('admin.scholarships.store'), [
            'student_id' => $student->id,
            'percentage' => 50,
            'title' => 'Academic Excellence',
            'is_active' => true
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect();

        $this->assertDatabaseHas('scholarships', [
            'student_id' => $student->id,
            'percentage' => 50
        ]);
    }

    public function test_can_create_refund_flow()
    {
        $studentId = DB::table('students')->insertGetId([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'student_number' => 'STU-103',
            'branch_id' => $this->branch->id,
            'status' => 'Active',
            'gender' => 'Male'
        ]);

        $invoice = Invoice::create([
            'invoice_number' => 'INV-100',
            'student_id' => $studentId,
            'total_amount' => 1000,
            'paid_amount' => 1000,
            'status' => 'Paid',
            'issue_date' => now(),
            'due_date' => now()
        ]);

        $payment = Payment::create([
            'invoice_id' => $invoice->id,
            'student_id' => $studentId,
            'payment_number' => 'PAY-100',
            'amount' => 1000,
            'payment_method' => 'Cash',
            'payment_date' => now()
        ]);

        $response = $this->actingAs($this->superAdmin)->post(route('admin.refunds.store'), [
            'payment_id' => $payment->id,
            'amount' => 500,
            'reason' => 'Overpaid',
            'refund_date' => now()->toDateString()
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect();

        $this->assertDatabaseHas('refunds', [
            'payment_id' => $payment->id,
            'amount' => 500
        ]);
    }
}
