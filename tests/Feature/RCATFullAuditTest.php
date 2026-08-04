<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Branch;
use App\Models\Role;
use App\Models\Teacher;
use App\Models\Student;
use App\Models\StudentGuardian;
use App\Models\Classroom;
use App\Models\Course;
use App\Models\AcademicTerm;
use App\Models\SystemIdentity;
use App\Domain\Institution\Models\InstitutionSetting;
use Illuminate\Support\Facades\Route;

class RCATFullAuditTest extends TestCase
{
    use RefreshDatabase;

    protected $branch;
    protected $superAdmin;
    protected User $branchAdmin;
    protected User $teacherUser;
    protected User $studentUser;
    protected User $parentUser;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->withoutMiddleware([
            \App\Http\Middleware\CheckOnboardingStatus::class,
        ]);

        $this->seed(\Database\Seeders\FeatureSeeder::class);
        $this->seed(\Database\Seeders\PackageSeeder::class);
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);

        // System identity & term
        SystemIdentity::firstOrCreate(['company_name' => 'Demo Dershane'], ['uuid' => '12345', 'brand_name' => 'Dershane']);
        AcademicTerm::firstOrCreate(['is_active' => true], ['name' => '2025-2026', 'start_date' => now(), 'end_date' => now()->addYear()]);

        // Branch
        $this->branch = Branch::create(['name' => 'Kadıköy Şubesi', 'code' => 'KDK', 'slug' => 'kadikoy', 'status' => 'active']);
        InstitutionSetting::create(['branch_id' => $this->branch->id, 'institution_name' => 'Kadıköy Dershanesi', 'phone' => '02120000000', 'email' => 'kdk@test.com', 'address' => 'Kadıköy']);

        // Roles
        $superRole = Role::firstOrCreate(['name' => 'Super Admin']);
        $adminRole = Role::firstOrCreate(['name' => 'Branch Admin']);
        $teacherRole = Role::firstOrCreate(['name' => 'Teacher']);
        $studentRole = Role::firstOrCreate(['name' => 'Student']);
        $parentRole = Role::firstOrCreate(['name' => 'Parent']);

        // Users
        $this->superAdmin = User::factory()->create(['branch_id' => null, 'status' => 'ACTIVE']);
        $this->superAdmin->roles()->attach($superRole);

        $this->branchAdmin = User::factory()->create(['branch_id' => $this->branch->id, 'status' => 'ACTIVE']);
        $this->branchAdmin->roles()->attach($adminRole);
        $userPerms = \App\Models\Permission::where('name', 'like', 'users.%')->pluck('id');
        $adminRole->permissions()->syncWithoutDetaching($userPerms);

        $this->teacherUser = User::factory()->create(['branch_id' => $this->branch->id, 'status' => 'ACTIVE']);
        $this->teacherUser->roles()->attach($teacherRole);
        Teacher::create(['user_id' => $this->teacherUser->id, 'branch_id' => $this->branch->id, 'title' => 'Matematik', 'specialties' => 'Matematik', 'status' => 'ACTIVE']);

        $classroom = Classroom::create(['name' => '12-A', 'code' => '12A', 'branch_id' => $this->branch->id, 'capacity' => 30]);

        $this->studentUser = User::factory()->create(['branch_id' => $this->branch->id, 'status' => 'ACTIVE']);
        $this->studentUser->roles()->attach($studentRole);
        $student = Student::create(['user_id' => $this->studentUser->id, 'branch_id' => $this->branch->id, 'classroom_id' => $classroom->id, 'first_name' => 'Ali', 'last_name' => 'Yılmaz', 'student_number' => 'STD-001', 'status' => 'ACTIVE']);

        $this->parentUser = User::factory()->create(['branch_id' => $this->branch->id, 'status' => 'ACTIVE']);
        $this->parentUser->roles()->attach($parentRole);
        StudentGuardian::create(['user_id' => $this->parentUser->id, 'student_id' => $student->id, 'guardian_name' => 'Veli Yılmaz', 'relation' => 'FATHER', 'phone' => '05550000000', 'is_primary' => true]);
    }

    public function test_full_system_rcat_audit_across_all_roles_and_routes(): void
    {
        $users = [
            'Super Admin' => $this->superAdmin,
            'Branch Admin' => $this->branchAdmin,
            'Teacher' => $this->teacherUser,
            'Student' => $this->studentUser,
            'Parent' => $this->parentUser,
        ];

        $routeCollection = Route::getRoutes();
        $routes = [];

        foreach ($routeCollection as $route) {
            $methods = $route->methods();
            if (in_array('GET', $methods) || in_array('HEAD', $methods)) {
                $uri = $route->uri();
                if (str_starts_with($uri, '_') || str_starts_with($uri, 'sanctum')) {
                    continue;
                }

                $testUri = preg_replace('/\{[a-zA-Z0-9_]+\}/', '1', $uri);
                $fullUrl = '/' . ltrim($testUri, '/');

                $routes[] = [
                    'uri' => $uri,
                    'testUrl' => $fullUrl,
                    'name' => $route->getName() ?? 'unnamed',
                    'middleware' => implode(', ', $route->middleware()),
                ];
            }
        }

        $auditData = [];

        foreach ($routes as $rInfo) {
            $url = $rInfo['testUrl'];
            $entry = [
                'uri' => $rInfo['uri'],
                'testUrl' => $url,
                'name' => $rInfo['name'],
                'middleware' => $rInfo['middleware'],
                'roles' => [],
            ];

            foreach ($users as $roleName => $user) {
                try {
                    session(['active_branch_id' => $user->branch_id ?? $this->branch->id]);
                    $response = $this->actingAs($user)->get($url);
                    $status = $response->getStatusCode();
                } catch (\Throwable $e) {
                    $status = 500;
                }

                $entry['roles'][$roleName] = $status;
            }

            $auditData[] = $entry;
        }

        @mkdir(base_path('scratch'), 0777, true);
        file_put_contents(base_path('scratch/rcat_audit_data.json'), json_encode($auditData, JSON_PRETTY_PRINT));

        $this->assertNotEmpty($auditData);
    }
}
