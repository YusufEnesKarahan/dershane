<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Classroom;
use App\Models\ContactMessage;
use App\Models\PlatformSetting;
use App\Models\Exam;
use App\Models\Payroll;
use App\Models\LeaveRequest;
use App\Models\StudentAdmission;
use App\Models\Branch;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PolicyHardeningTest extends TestCase
{
    use DatabaseTransactions;

    protected User $unauthorizedUser;
    protected User $authorizedUser;
    protected User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Roles
        $parentRole = Role::firstOrCreate(['name' => 'Parent']);
        $adminRole = Role::firstOrCreate(['name' => 'Administrator']);

        // 2. Users
        $this->unauthorizedUser = User::create([
            'name' => 'No Perm User',
            'email' => 'noperms@test.com',
            'password' => bcrypt('password'),
            'status' => \App\Enums\UserStatus::ACTIVE
        ]);
        $this->unauthorizedUser->roles()->attach($parentRole->id);

        $this->adminUser = User::create([
            'name' => 'Admin User',
            'email' => 'adminperms@test.com',
            'password' => bcrypt('password'),
            'status' => \App\Enums\UserStatus::ACTIVE
        ]);
        $this->adminUser->roles()->attach($adminRole->id);

        // Authorized user will have direct roles/permissions attached
        $this->authorizedUser = User::create([
            'name' => 'Perm User',
            'email' => 'hasperms@test.com',
            'password' => bcrypt('password'),
            'status' => \App\Enums\UserStatus::ACTIVE
        ]);
        
        // Let's create permissions in the system for testing
        $permissions = [
            'students.view', 'students.create', 'students.update', 'students.delete',
            'teachers.view', 'teachers.create', 'teachers.update', 'teachers.delete',
            'classrooms.view', 'classrooms.manage',
            'contacts.view', 'contacts.create', 'contacts.update', 'contacts.delete',
            'settings.view', 'settings.update',
            'admission.view', 'admission.create', 'admission.update', 'admission.approve',
            'enrollment.manage', 'hr.view', 'hr.create', 'hr.update', 'hr.approve',
            'payroll.manage', 'dashboard.view'
        ];

        // Attach role or directly check custom permission Resolver
        // For test isolation, we'll assign the permissions using permission resolver mocks or directly seeding permissions to roles.
        // Let's seed permissions to the database and attach them to a custom role, then assign to authorizedUser.
        $testRole = Role::firstOrCreate(['name' => 'TestAuthorizedRole']);
        
        // Find or create the permissions
        foreach ($permissions as $permName) {
            $perm = Permission::firstOrCreate(['name' => $permName]);
            $testRole->permissions()->syncWithoutDetaching([$perm->id]);
        }
        
        $this->authorizedUser->roles()->attach($testRole->id);

        // Warm up user permission resolver cache
        app(\App\Domain\Auth\Services\PermissionCache::class)->clearUserCache($this->unauthorizedUser);
        app(\App\Domain\Auth\Services\PermissionCache::class)->clearUserCache($this->authorizedUser);
        app(\App\Domain\Auth\Services\PermissionCache::class)->clearUserCache($this->adminUser);
    }

    public function test_student_policy_enforces_correct_permissions(): void
    {
        $student = new Student();

        // 1. Unauthorized
        $this->assertFalse($this->unauthorizedUser->can('viewAny', Student::class));
        $this->assertFalse($this->unauthorizedUser->can('view', $student));
        $this->assertFalse($this->unauthorizedUser->can('create', Student::class));
        $this->assertFalse($this->unauthorizedUser->can('update', $student));
        $this->assertFalse($this->unauthorizedUser->can('delete', $student));

        // 2. Authorized
        $this->assertTrue($this->authorizedUser->can('viewAny', Student::class));
        $this->assertTrue($this->authorizedUser->can('view', $student));
        $this->assertTrue($this->authorizedUser->can('create', Student::class));
        $this->assertTrue($this->authorizedUser->can('update', $student));
        $this->assertTrue($this->authorizedUser->can('delete', $student));

        // 3. Administrator
        $this->assertTrue($this->adminUser->can('viewAny', Student::class));
        $this->assertTrue($this->adminUser->can('view', $student));
        $this->assertTrue($this->adminUser->can('create', Student::class));
        $this->assertTrue($this->adminUser->can('update', $student));
        $this->assertTrue($this->adminUser->can('delete', $student));
    }

    public function test_teacher_policy_enforces_correct_permissions(): void
    {
        $teacher = new Teacher();

        // 1. Unauthorized
        $this->assertFalse($this->unauthorizedUser->can('viewAny', Teacher::class));
        $this->assertFalse($this->unauthorizedUser->can('view', $teacher));
        $this->assertFalse($this->unauthorizedUser->can('create', Teacher::class));
        $this->assertFalse($this->unauthorizedUser->can('update', $teacher));
        $this->assertFalse($this->unauthorizedUser->can('delete', $teacher));

        // 2. Authorized
        $this->assertTrue($this->authorizedUser->can('viewAny', Teacher::class));
        $this->assertTrue($this->authorizedUser->can('view', $teacher));
        $this->assertTrue($this->authorizedUser->can('create', Teacher::class));
        $this->assertTrue($this->authorizedUser->can('update', $teacher));
        $this->assertTrue($this->authorizedUser->can('delete', $teacher));

        // 3. Administrator
        $this->assertTrue($this->adminUser->can('viewAny', Teacher::class));
        $this->assertTrue($this->adminUser->can('view', $teacher));
        $this->assertTrue($this->adminUser->can('create', Teacher::class));
        $this->assertTrue($this->adminUser->can('update', $teacher));
        $this->assertTrue($this->adminUser->can('delete', $teacher));
    }

    public function test_contact_message_policy_enforces_correct_permissions(): void
    {
        $message = new ContactMessage();

        // 1. Unauthorized
        $this->assertFalse($this->unauthorizedUser->can('viewAny', ContactMessage::class));
        $this->assertFalse($this->unauthorizedUser->can('view', $message));
        $this->assertFalse($this->unauthorizedUser->can('create', ContactMessage::class));
        $this->assertFalse($this->unauthorizedUser->can('update', $message));
        $this->assertFalse($this->unauthorizedUser->can('delete', $message));

        // 2. Authorized
        $this->assertTrue($this->authorizedUser->can('viewAny', ContactMessage::class));
        $this->assertTrue($this->authorizedUser->can('view', $message));
        $this->assertTrue($this->authorizedUser->can('create', ContactMessage::class));
        $this->assertTrue($this->authorizedUser->can('update', $message));
        $this->assertTrue($this->authorizedUser->can('delete', $message));

        // 3. Administrator
        $this->assertTrue($this->adminUser->can('viewAny', ContactMessage::class));
        $this->assertTrue($this->adminUser->can('view', $message));
        $this->assertTrue($this->adminUser->can('create', ContactMessage::class));
        $this->assertTrue($this->adminUser->can('update', $message));
        $this->assertTrue($this->adminUser->can('delete', $message));
    }

    public function test_classroom_policy_enforces_correct_permissions(): void
    {
        $classroom = new Classroom();

        // 1. Unauthorized
        $this->assertFalse($this->unauthorizedUser->can('viewAny', Classroom::class));
        $this->assertFalse($this->unauthorizedUser->can('view', $classroom));
        $this->assertFalse($this->unauthorizedUser->can('create', Classroom::class));
        $this->assertFalse($this->unauthorizedUser->can('update', $classroom));
        $this->assertFalse($this->unauthorizedUser->can('delete', $classroom));

        // 2. Authorized
        $this->assertTrue($this->authorizedUser->can('viewAny', Classroom::class));
        $this->assertTrue($this->authorizedUser->can('view', $classroom));
        $this->assertTrue($this->authorizedUser->can('create', Classroom::class));
        $this->assertTrue($this->authorizedUser->can('update', $classroom));
        $this->assertTrue($this->authorizedUser->can('delete', $classroom));
    }

    public function test_platform_setting_policy_enforces_correct_permissions(): void
    {
        $setting = new PlatformSetting();

        // 1. Unauthorized
        $this->assertFalse($this->unauthorizedUser->can('viewAny', PlatformSetting::class));
        $this->assertFalse($this->unauthorizedUser->can('update', $setting));

        // 2. Authorized
        $this->assertTrue($this->authorizedUser->can('viewAny', PlatformSetting::class));
        $this->assertTrue($this->authorizedUser->can('update', $setting));
    }

    public function test_admission_policy_enforces_correct_permissions(): void
    {
        $admission = new StudentAdmission();

        // 1. Unauthorized
        $this->assertFalse($this->unauthorizedUser->can('viewAny', StudentAdmission::class));
        $this->assertFalse($this->unauthorizedUser->can('view', $admission));
        $this->assertFalse($this->unauthorizedUser->can('create', StudentAdmission::class));
        $this->assertFalse($this->unauthorizedUser->can('update', $admission));
        $this->assertFalse($this->unauthorizedUser->can('approve', $admission));
        $this->assertFalse($this->unauthorizedUser->can('delete', $admission));

        // 2. Authorized
        $this->assertTrue($this->authorizedUser->can('viewAny', StudentAdmission::class));
        $this->assertTrue($this->authorizedUser->can('view', $admission));
        $this->assertTrue($this->authorizedUser->can('create', StudentAdmission::class));
        $this->assertTrue($this->authorizedUser->can('update', $admission));
        $this->assertTrue($this->authorizedUser->can('approve', $admission));
        $this->assertTrue($this->authorizedUser->can('delete', $admission));
    }
}
