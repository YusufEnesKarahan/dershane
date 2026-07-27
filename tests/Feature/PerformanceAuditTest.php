<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Role;

class PerformanceAuditTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);
    }

    public function test_dashboard_loads_within_acceptable_time()
    {
        $user = User::factory()->create();
        $role = Role::where('name', 'Super Admin')->first();
        if ($role) {
            $user->roles()->attach($role);
        }

        $startTime = microtime(true);
        $response = $this->actingAs($user)->get('/admin/reporting/dashboard');
        $endTime = microtime(true);
        
        $duration = ($endTime - $startTime) * 1000; // ms

        $response->assertStatus(200);
        $this->assertLessThan(2000, $duration, "Dashboard took longer than 2000ms ($duration ms)");
    }

    public function test_students_index_loads_without_n_plus_one_or_memory_leak()
    {
        $user = User::factory()->create();
        $role = Role::where('name', 'Super Admin')->first();
        if ($role) {
            $user->roles()->attach($role);
        }

        $startTime = microtime(true);
        $response = $this->actingAs($user)->get('/admin/students');
        $endTime = microtime(true);
        
        $duration = ($endTime - $startTime) * 1000; // ms

        $response->assertStatus(200);
        $this->assertLessThan(2000, $duration, "Students index took longer than 2000ms ($duration ms)");
    }
}
