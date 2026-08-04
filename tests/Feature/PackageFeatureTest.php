<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Branch;
use App\Models\Role;
use App\Domain\Package\Models\Package;
use App\Domain\Package\Models\Feature;
use App\Domain\Package\Models\BranchPackage;
use App\Domain\Package\Services\PackageService;
use App\Domain\Auth\Services\VisibilityResolver;
use Database\Seeders\FeatureSeeder;
use Database\Seeders\PackageSeeder;

class PackageFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected PackageService $packageService;
    protected Branch $branchV1;
    protected Branch $branchV2;
    protected Branch $branchV3;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(\App\Http\Middleware\CheckOnboardingStatus::class);

        $this->seed(FeatureSeeder::class);
        $this->seed(PackageSeeder::class);

        $this->packageService = app(PackageService::class);

        // Create test branches
        $this->branchV1 = Branch::create(['name' => 'Şube V1 Starter', 'code' => 'BR-V1', 'slug' => 'br-v1', 'status' => 'active']);
        $this->branchV2 = Branch::create(['name' => 'Şube V2 Pro', 'code' => 'BR-V2', 'slug' => 'br-v2', 'status' => 'active']);
        $this->branchV3 = Branch::create(['name' => 'Şube V3 Enterprise', 'code' => 'BR-V3', 'slug' => 'br-v3', 'status' => 'active']);

        $pkgV1 = Package::where('code', 'V1')->firstOrFail();
        $pkgV2 = Package::where('code', 'V2')->firstOrFail();
        $pkgV3 = Package::where('code', 'V3')->firstOrFail();

        // Assign packages
        $this->packageService->changeBranchPackage($this->branchV1, $pkgV1->id);
        $this->packageService->changeBranchPackage($this->branchV2, $pkgV2->id);
        $this->packageService->changeBranchPackage($this->branchV3, $pkgV3->id);
    }

    public function test_v1_package_cannot_access_exam_module(): void
    {
        $this->assertFalse($this->packageService->hasFeature($this->branchV1, 'exam'));
        $this->assertFalse($this->packageService->hasFeature($this->branchV1, 'attendance'));
        $this->assertFalse($this->packageService->hasFeature($this->branchV1, 'finance'));
        
        // V1 should have student and schedule
        $this->assertTrue($this->packageService->hasFeature($this->branchV1, 'student'));
        $this->assertTrue($this->packageService->hasFeature($this->branchV1, 'schedule'));
    }

    public function test_v2_package_can_access_exam_and_attendance_but_not_finance_or_guidance(): void
    {
        $this->assertTrue($this->packageService->hasFeature($this->branchV2, 'exam'));
        $this->assertTrue($this->packageService->hasFeature($this->branchV2, 'attendance'));
        $this->assertTrue($this->packageService->hasFeature($this->branchV2, 'homework'));
        $this->assertTrue($this->packageService->hasFeature($this->branchV2, 'notification'));

        // Closed in V2
        $this->assertFalse($this->packageService->hasFeature($this->branchV2, 'finance'));
        $this->assertFalse($this->packageService->hasFeature($this->branchV2, 'guidance'));
    }

    public function test_v3_package_has_all_features_enabled(): void
    {
        $features = ['student', 'teacher', 'classroom', 'schedule', 'attendance', 'exam', 'homework', 'notification', 'guidance', 'finance', 'reports'];

        foreach ($features as $code) {
            $this->assertTrue($this->packageService->hasFeature($this->branchV3, $code), "Feature {$code} should be enabled in V3");
        }
    }

    public function test_feature_middleware_blocks_access_when_feature_is_disabled(): void
    {
        $branchAdmin = User::factory()->create([
            'branch_id' => $this->branchV1->id,
        ]);
        $role = Role::firstOrCreate(['name' => 'Branch Admin']);
        $branchAdmin->roles()->attach($role);
        $branchAdmin->unsetRelation('roles');

        // V1 branch user attempting to access exams route should receive 403
        $response = $this->actingAs($branchAdmin)
            ->get(route('admin.exams.index'));

        $response->assertStatus(403);
    }

    public function test_visibility_resolver_hides_menu_item_when_feature_is_disabled(): void
    {
        $branchAdmin = User::factory()->create([
            'branch_id' => $this->branchV1->id,
        ]);
        $role = Role::firstOrCreate(['name' => 'Branch Admin']);
        $branchAdmin->roles()->attach($role);
        $branchAdmin->unsetRelation('roles');

        $resolver = app(VisibilityResolver::class);

        // Feature exam is closed for V1
        $isVisible = $resolver->resolve($branchAdmin, 'students.view', null, 'exam');
        $this->assertFalse($isVisible);
    }

    public function test_tenant_isolation_maintains_independent_feature_states_per_branch(): void
    {
        // Branch 1 (V1) exam is disabled
        $this->assertFalse($this->packageService->hasFeature($this->branchV1, 'exam'));

        // Branch 2 (V2) exam is enabled
        $this->assertTrue($this->packageService->hasFeature($this->branchV2, 'exam'));

        // Branch 3 (V3) finance is enabled, but V2 finance is disabled
        $this->assertFalse($this->packageService->hasFeature($this->branchV2, 'finance'));
        $this->assertTrue($this->packageService->hasFeature($this->branchV3, 'finance'));
    }
}
