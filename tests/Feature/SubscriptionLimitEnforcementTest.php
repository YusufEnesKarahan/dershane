<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\License;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use App\Models\Student;
use App\Models\Classroom;
use App\Domain\Student\Services\StudentService;
use App\DTOs\Student\CreateStudentDTO;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Auth\Access\AuthorizationException;
use Tests\TestCase;

class SubscriptionLimitEnforcementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        \App\Models\SystemIdentity::create(['company_name' => 'Test', 'product_name' => 'Test ERP']);
        \App\Models\AcademicTerm::create(['name' => '2025-2026', 'start_date' => now(), 'end_date' => now()->addYear(), 'is_active' => true]);
    }

    public function test_student_creation_blocked_when_limit_exceeded()
    {
        $branch = Branch::create(['name' => 'Limited Branch', 'slug' => 'limited-branch']);
        $user = User::factory()->create(['branch_id' => $branch->id]);

        $plan = Plan::create(['name' => 'Starter', 'slug' => 'starter', 'price' => 100, 'is_active' => true, 'max_students' => 1]);
        $license = License::create(['license_key' => 'TEST-1', 'status' => 'active', 'plan_id' => $plan->id, 'plan' => $plan->slug]);
        Subscription::create([
            'license_id' => $license->id,
            'branch_id' => $branch->id,
            'plan_id' => $plan->id,
            'status' => 'active',
            'starts_at' => now(),
            'ends_at' => now()->addMonth(),
        ]);

        $classroom = Classroom::create(['name' => '10-A', 'code' => '10A', 'branch_id' => $branch->id, 'capacity' => 20]);

        // First student - should succeed
        Student::create([
            'student_number' => 'STU-001',
            'identity_number' => '12345678901',
            'first_name' => 'First',
            'last_name' => 'Student',
            'birth_date' => '2010-01-01',
            'gender' => 'M',
            'branch_id' => $branch->id,
            'classroom_id' => $classroom->id,
            'status' => 'active',
        ]);

        // Second student via Service - should fail
        $service = app(StudentService::class);
        $dto = new CreateStudentDTO(
            student_number: 'STU-002',
            identity_number: '12345678902',
            first_name: 'Test',
            last_name: 'Student',
            birth_date: '2010-01-01',
            gender: 'M',
            branch_id: $branch->id,
            classroom_id: $classroom->id,
            status: 'active'
        );

        $this->actingAs($user);
        
        $this->expectException(AuthorizationException::class);
        $this->expectExceptionMessage('Mevcut abonelik planı öğrenci eklemeye izin vermiyor.');
        
        $service->create($dto);
    }
}
