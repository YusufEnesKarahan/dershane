<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Branch;
use App\Models\License;
use App\Models\Student;

class LicenseLimitTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setupSaaSTenant();
    }

    public function test_cannot_add_student_if_limit_reached()
    {
        // Arrange
        $license = License::create([
            'license_key' => 'TEST-123',
            'status' => 'active',
            'metadata' => [
                'max_students' => 1
            ]
        ]);

        \Illuminate\Support\Facades\DB::table('students')->insert([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'student_number' => '12345',
            'gender' => 'male',
            'branch_id' => $this->branch->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Act
        // Assuming we route to a store method with the 'limits:student' middleware
        // We'll create a dummy route just to test the middleware if a real route is too complex
        $this->app['router']->post('/test-student', function () {
            return response()->json(['success' => true]);
        })->middleware('limits:student');

        $response = $this->actingAs($this->tenantUser)->postJson('/test-student', []);

        // Assert
        $response->assertStatus(402);
    }

    public function test_can_add_student_if_under_limit()
    {
        // Arrange
        $license = License::create([
            'license_key' => 'TEST-124',
            'status' => 'active',
            'metadata' => [
                'max_students' => 5
            ]
        ]);

        \Illuminate\Support\Facades\DB::table('students')->insert([
            ['first_name' => 'A', 'last_name' => 'B', 'student_number' => '1', 'gender' => 'male', 'branch_id' => $this->branch->id, 'created_at' => now(), 'updated_at' => now()],
            ['first_name' => 'C', 'last_name' => 'D', 'student_number' => '2', 'gender' => 'female', 'branch_id' => $this->branch->id, 'created_at' => now(), 'updated_at' => now()],
        ]);

        $this->app['router']->post('/test-student', function () {
            return response()->json(['success' => true]);
        })->middleware('limits:student');

        // Act
        $response = $this->actingAs($this->tenantUser)->postJson('/test-student', []);

        // Assert
        $response->assertStatus(200);
    }
}
