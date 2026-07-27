<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\License;
use App\Models\FeatureFlag;
use App\Models\User;
use App\Models\Student;
use App\Models\Branch;
use App\Domain\Platform\Services\LicenseService;
use App\Domain\Platform\Services\FeatureFlagService;
use Illuminate\Support\Str;

class SaaSFoundationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);
    }

    public function test_license_active_check()
    {
        $license = License::create([
            'license_key' => Str::uuid()->toString(),
            'status' => 'active',
            'plan' => 'professional',
            'expires_at' => now()->addYear(),
        ]);

        $this->assertTrue($license->isActive());
        $this->assertFalse($license->isExpired());

        $service = new LicenseService();
        $service->clearCache();
        $this->assertTrue($service->isActive());
    }

    public function test_expired_license_check()
    {
        $license = License::create([
            'license_key' => Str::uuid()->toString(),
            'status' => 'active',
            'plan' => 'basic',
            'expires_at' => now()->subDay(),
        ]);

        $this->assertTrue($license->isExpired());
        $this->assertFalse($license->isActive());

        $service = new LicenseService();
        $service->clearCache();
        $this->assertTrue($service->isExpired());
        $this->assertFalse($service->isActive());
    }

    public function test_feature_flag_enabled()
    {
        FeatureFlag::create([
            'name' => 'advanced_reports',
            'enabled' => true,
            'metadata' => ['plan' => 'enterprise'],
        ]);

        $service = new FeatureFlagService();
        $service->clearCache('advanced_reports');
        $this->assertTrue($service->enabled('advanced_reports'));
        $this->assertFalse($service->disabled('advanced_reports'));
    }

    public function test_feature_flag_disabled()
    {
        FeatureFlag::create([
            'name' => 'sms_integration',
            'enabled' => false,
        ]);

        $service = new FeatureFlagService();
        $service->clearCache('sms_integration');
        $this->assertFalse($service->enabled('sms_integration'));
        $this->assertTrue($service->disabled('sms_integration'));

        // Non-existent flags should also be disabled
        $service->clearCache('nonexistent_feature');
        $this->assertFalse($service->enabled('nonexistent_feature'));
    }

    public function test_user_data_isolation_by_branch()
    {
        $branchA = Branch::create(['name' => 'Şube A', 'slug' => 'sube-a']);
        $branchB = Branch::create(['name' => 'Şube B', 'slug' => 'sube-b']);

        // Create students in separate branches
        Student::create([
            'student_number' => 'STU-A-001',
            'first_name' => 'Ali',
            'last_name' => 'Yılmaz',
            'branch_id' => $branchA->id,
            'status' => 'Active',
        ]);

        Student::create([
            'student_number' => 'STU-A-002',
            'first_name' => 'Ayşe',
            'last_name' => 'Kaya',
            'branch_id' => $branchA->id,
            'status' => 'Active',
        ]);

        Student::create([
            'student_number' => 'STU-B-001',
            'first_name' => 'Mehmet',
            'last_name' => 'Demir',
            'branch_id' => $branchB->id,
            'status' => 'Active',
        ]);

        // Branch A should see only its own students
        $branchAStudents = Student::where('branch_id', $branchA->id)->get();
        $this->assertCount(2, $branchAStudents);

        // Branch B should see only its own student
        $branchBStudents = Student::where('branch_id', $branchB->id)->get();
        $this->assertCount(1, $branchBStudents);

        // Verify no cross-branch leakage
        $branchAStudents->each(function ($student) use ($branchA) {
            $this->assertEquals($branchA->id, $student->branch_id);
        });
    }
}
