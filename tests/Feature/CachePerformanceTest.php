<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Student;
use App\Models\Lead;
use Illuminate\Support\Facades\Cache;
use App\Domain\Student\Services\StudentAnalyticsService;
use App\Domain\Reporting\Services\ExecutiveDashboardService;
use App\Domain\Auth\Services\MenuBuilder;
use App\Domain\Auth\Services\PermissionCache;

class CachePerformanceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);
    }

    public function test_student_analytics_service_caching_and_invalidation()
    {
        $service = app(StudentAnalyticsService::class);

        // Fetch metrics to populate cache
        $metricsBefore = $service->getSummary();
        $this->assertTrue(Cache::has('students.analytics.summary'));

        // Save a new student and trigger invalidation
        $student = Student::factory()->create(['status' => 'Active']);
        
        $this->assertFalse(Cache::has('students.analytics.summary'));
    }

    public function test_executive_dashboard_service_caching()
    {
        $service = app(ExecutiveDashboardService::class);

        $metricsBefore = $service->getMetrics();
        $this->assertTrue(Cache::has('executive_dashboard_metrics'));

        // Save a lead to trigger invalidation
        $lead = Lead::factory()->create();
        
        $this->assertFalse(Cache::has('executive_dashboard_metrics'));
    }

    public function test_menu_builder_caching_and_invalidation()
    {
        $user = User::factory()->create();
        $menuBuilder = app(MenuBuilder::class);
        $permissionCache = app(PermissionCache::class);

        // Build menu to populate cache
        $menu = $menuBuilder->build($user);
        $this->assertTrue(Cache::has('user.menu.' . $user->id));

        // Clear user cache to trigger menu invalidation
        $permissionCache->clearUserCache($user);
        $this->assertFalse(Cache::has('user.menu.' . $user->id));
    }
}
