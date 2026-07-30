<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\HQTenant;
use App\Models\HQSystemInstance;
use App\Models\HQSubscriptionPlan;
use App\Models\HQQuotaRule;
use App\Domain\HQ\Services\HQSubscriptionService;
use App\Domain\HQ\Services\HQUsageService;
use App\Domain\HQ\Services\UsageAggregationService;
use Carbon\Carbon;

class HQUsageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->admin = User::factory()->create();
    }

    public function test_can_record_usage_metrics()
    {
        $tenant = HQTenant::create([
            'name' => 'Usage Test Tenant',
            'slug' => 'usage-test',
            'db_name' => 'tenant_usage',
            'status' => 'active',
        ]);

        $usageService = app(HQUsageService::class);
        $usageService->recordMetrics($tenant, [
            'students' => 150,
            'storage_bytes' => 1024 * 1024 * 500, // 500MB
            'api_requests' => 1200,
        ]);

        $this->assertDatabaseHas('hq_usage_metrics', [
            'tenant_id' => $tenant->id,
            'metric_key' => 'students',
            'metric_value' => '150.00',
        ]);
        
        $this->assertDatabaseHas('hq_usage_metrics', [
            'tenant_id' => $tenant->id,
            'metric_key' => 'storage_bytes',
            'metric_value' => '524288000.00',
        ]);
        
        $this->assertDatabaseHas('hq_usage_metrics', [
            'tenant_id' => $tenant->id,
            'metric_key' => 'api_requests',
            'metric_value' => '1200.00',
        ]);
        
        // 3 assertions
    }

    public function test_quota_evaluation_triggers_violations_based_on_plan()
    {
        $tenant = HQTenant::create([
            'name' => 'Quota Test Tenant',
            'slug' => 'quota-test',
            'db_name' => 'tenant_quota',
            'status' => 'active',
        ]);

        $plan = HQSubscriptionPlan::create([
            'name' => 'Basic Plan',
            'slug' => 'basic',
            'billing_period' => 'monthly',
            'price' => 99.99,
            'currency' => 'USD',
            'limits' => ['students' => 100],
            'is_active' => true,
        ]);

        $subService = app(HQSubscriptionService::class);
        $subService->createSubscription($tenant, $plan);

        $usageService = app(HQUsageService::class);
        
        // Trigger Warning (85 students out of 100 limit, default warning is 80%)
        $usageService->recordMetrics($tenant, ['students' => 85]);

        $this->assertDatabaseHas('hq_quota_violations', [
            'tenant_id' => $tenant->id,
            'metric_key' => 'students',
            'severity' => 'warning',
            'actual_value' => '85.00',
        ]);
        $this->assertDatabaseMissing('hq_quota_violations', [
            'tenant_id' => $tenant->id,
            'metric_key' => 'students',
            'severity' => 'critical',
        ]);

        // Trigger Critical (105 students)
        $usageService->recordMetrics($tenant, ['students' => 105]);

        $this->assertDatabaseHas('hq_quota_violations', [
            'tenant_id' => $tenant->id,
            'metric_key' => 'students',
            'severity' => 'critical',
            'actual_value' => '105.00',
        ]);
        
        // Warning should be resolved
        $this->assertDatabaseMissing('hq_quota_violations', [
            'tenant_id' => $tenant->id,
            'metric_key' => 'students',
            'severity' => 'warning',
            'resolved_at' => null,
        ]);
        
        // 4 assertions
    }

    public function test_custom_quota_rules_override_plan_limits()
    {
        $tenant = HQTenant::create([
            'name' => 'Custom Rule Tenant',
            'slug' => 'custom-rule',
            'db_name' => 'tenant_custom',
            'status' => 'active',
        ]);

        $plan = HQSubscriptionPlan::create([
            'name' => 'Basic Plan',
            'slug' => 'basic-2',
            'billing_period' => 'monthly',
            'price' => 99.99,
            'currency' => 'USD',
            'limits' => ['api_requests' => 1000],
            'is_active' => true,
        ]);

        $subService = app(HQSubscriptionService::class);
        $subService->createSubscription($tenant, $plan);

        HQQuotaRule::create([
            'tenant_id' => $tenant->id,
            'metric_key' => 'api_requests',
            'warning_threshold' => 1800,
            'critical_threshold' => 2000,
            'is_active' => true,
        ]);

        $usageService = app(HQUsageService::class);
        
        // 1500 API calls (over plan limit, but under custom limit)
        $usageService->recordMetrics($tenant, ['api_requests' => 1500]);
        
        $this->assertDatabaseMissing('hq_quota_violations', [
            'tenant_id' => $tenant->id,
            'metric_key' => 'api_requests',
        ]);

        // 1900 API calls (trigger custom warning)
        $usageService->recordMetrics($tenant, ['api_requests' => 1900]);
        
        $this->assertDatabaseHas('hq_quota_violations', [
            'tenant_id' => $tenant->id,
            'metric_key' => 'api_requests',
            'severity' => 'warning',
            'actual_value' => '1900.00',
        ]);
        
        // 3 assertions
    }

    public function test_usage_aggregation_hourly()
    {
        $tenant = HQTenant::create([
            'name' => 'Agg Test Tenant',
            'slug' => 'agg-test',
            'db_name' => 'tenant_agg',
            'status' => 'active',
        ]);

        $usageService = app(HQUsageService::class);
        
        // Insert metrics exactly 1 hour ago
        $reportedAt = now()->subHour()->startOfHour()->addMinutes(15);
        $usageService->recordMetrics($tenant, ['students' => 50], $reportedAt);
        
        $reportedAt2 = now()->subHour()->startOfHour()->addMinutes(45);
        $usageService->recordMetrics($tenant, ['students' => 70, 'teachers' => 10], $reportedAt2);

        $aggService = app(UsageAggregationService::class);
        $aggService->aggregate('hourly');

        $this->assertDatabaseHas('hq_usage_snapshots', [
            'tenant_id' => $tenant->id,
            'period' => 'hourly',
        ]);

        $snapshot = \App\Models\HQUsageSnapshot::where('tenant_id', $tenant->id)
            ->where('period', 'hourly')
            ->first();
            
        $this->assertNotNull($snapshot);
        $data = $snapshot->data_json;
        $this->assertEquals(70, $data['students']);
        $this->assertEquals(10, $data['teachers']);
        
        // 4 assertions
    }

    public function test_usage_aggregation_daily_from_hourly()
    {
        $tenant = HQTenant::create([
            'name' => 'Agg Daily Tenant',
            'slug' => 'agg-daily',
            'db_name' => 'tenant_agg2',
            'status' => 'active',
        ]);

        // Seed 2 hourly snapshots from yesterday
        \App\Models\HQUsageSnapshot::create([
            'tenant_id' => $tenant->id,
            'period' => 'hourly',
            'period_start' => now()->subDay()->startOfDay()->addHours(1),
            'period_end' => now()->subDay()->startOfDay()->addHours(2),
            'data_json' => ['students' => 100, 'emails_sent' => 500],
        ]);
        
        \App\Models\HQUsageSnapshot::create([
            'tenant_id' => $tenant->id,
            'period' => 'hourly',
            'period_start' => now()->subDay()->startOfDay()->addHours(3),
            'period_end' => now()->subDay()->startOfDay()->addHours(4),
            'data_json' => ['students' => 120, 'emails_sent' => 800],
        ]);

        $aggService = app(UsageAggregationService::class);
        $aggService->aggregate('daily');

        $snapshot = \App\Models\HQUsageSnapshot::where('tenant_id', $tenant->id)
            ->where('period', 'daily')
            ->first();
            
        $this->assertNotNull($snapshot);
        $data = $snapshot->data_json;
        $this->assertEquals(120, $data['students']);
        $this->assertEquals(800, $data['emails_sent']); // Since we take max right now
        
        // 3 assertions
    }

    public function test_api_usage_report_endpoint()
    {
        $tenant = HQTenant::create([
            'name' => 'API Tenant',
            'slug' => 'api-tenant',
            'db_name' => 'tenant_api',
            'status' => 'active',
        ]);

        $instance = HQSystemInstance::create([
            'tenant_id' => $tenant->id,
            'system_uuid' => 'sys-api-123',
            'system_name' => 'Main',
            'system_version' => '1.0.0',
            'status' => 'online',
        ]);

        $this->withoutMiddleware(\App\Http\Middleware\VerifyHQApiSignature::class);
        
        $response = $this->postJson('/api/hq/usage/report', [
            'system_id' => 'sys-api-123',
            'metrics' => [
                'students' => 450,
                'teachers' => 30
            ]
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['status', 'message', 'quotas']);
        
        $this->assertDatabaseHas('hq_usage_metrics', [
            'tenant_id' => $tenant->id,
            'metric_key' => 'students',
            'metric_value' => '450.00',
        ]);
    }

    public function test_api_quota_endpoint()
    {
        $tenant = HQTenant::create([
            'name' => 'Quota API Tenant',
            'slug' => 'quota-api-tenant',
            'db_name' => 'tenant_quota_api',
            'status' => 'active',
        ]);

        $instance = HQSystemInstance::create([
            'tenant_id' => $tenant->id,
            'system_uuid' => 'sys-quota-123',
            'system_name' => 'Main',
            'system_version' => '1.0.0',
            'status' => 'online',
        ]);
        
        $plan = HQSubscriptionPlan::create([
            'name' => 'API Plan',
            'slug' => 'api-plan',
            'billing_period' => 'monthly',
            'price' => 99.99,
            'currency' => 'USD',
            'limits' => ['students' => 500, 'storage' => 10],
            'is_active' => true,
        ]);
        
        $subService = app(HQSubscriptionService::class);
        $subService->createSubscription($tenant, $plan);

        $this->withoutMiddleware(\App\Http\Middleware\VerifyHQApiSignature::class);
        
        $response = $this->getJson('/api/hq/quota?system_id=sys-quota-123');

        $response->assertStatus(200);
        $response->assertJsonPath('quotas.students', 500);
        $response->assertJsonPath('quotas.storage', 10);
    }
    
    public function test_web_tenant_usage_view()
    {
        $tenant = HQTenant::create([
            'name' => 'Web Tenant',
            'slug' => 'web-tenant',
            'db_name' => 'tenant_web',
            'status' => 'active',
        ]);
        
        $this->withoutMiddleware();
        
        // Skip permissions for test
        \Illuminate\Support\Facades\Gate::before(function () {
            return true;
        });
        
        $response = $this->actingAs($this->admin)->get(route('admin.platform.hq_central.tenants.usage', $tenant));
        
        $response->assertStatus(200);
        $response->assertSee('Tenant Usage: Web Tenant');
        $response->assertSee('Subscription Quotas');
        // 3 assertions
    }
}
