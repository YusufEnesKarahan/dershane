<?php

namespace Tests\Feature\Tenant;

use App\Models\Branch;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TenantIsolationTest extends TestCase
{
    use RefreshDatabase;

    public function test_tenant_isolation_prevents_access_to_other_branch_data(): void
    {
        // Setup branches
        $branchA = Branch::factory()->create(['name' => 'Branch A', 'slug' => 'branch-a']);
        $branchB = Branch::factory()->create(['name' => 'Branch B', 'slug' => 'branch-b']);

        // Setup users
        $userA = User::factory()->create(['branch_id' => $branchA->id]);
        $userB = User::factory()->create(['branch_id' => $branchB->id]);

        // Setup data
        $studentA = new Student();
        $studentA->branch_id = $branchA->id;
        $studentA->first_name = 'Student A';
        $studentA->last_name = 'Lastname';
        $studentA->student_number = 'STU-001';
        $studentA->identity_number = '12345678901';
        $studentA->save();

        $studentB = new Student();
        $studentB->branch_id = $branchB->id;
        $studentB->first_name = 'Student B';
        $studentB->last_name = 'Lastname';
        $studentB->student_number = 'STU-002';
        $studentB->identity_number = '12345678902';
        $studentB->save();

        // Simulate User A logging in
        $this->actingAs($userA);

        // Ensure active branch context is set (this usually happens in EnsureActiveBranch middleware)
        \App\Core\Context\TenantContext::setActiveBranchId($userA->branch_id);

        // Try to retrieve Student B via eloquent
        $foundStudentB = Student::find($studentB->id);
        
        $this->assertNull($foundStudentB, 'User A should not be able to find Student B due to global scope.');
        
        // Ensure User A can find Student A
        $foundStudentA = Student::find($studentA->id);
        $this->assertNotNull($foundStudentA);
        $this->assertEquals($studentA->id, $foundStudentA->id);
        
        \App\Core\Context\TenantContext::clear();
    }
}
