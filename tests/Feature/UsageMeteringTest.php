<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\HQTenant;
use App\Models\HQPlan;
use App\Models\HQUsageRecord;
use App\Domain\HQ\Services\Billing\SubscriptionService;
use App\Domain\HQ\Services\Billing\EntitlementService;
use App\Domain\HQ\Services\HQAuditService;
use App\Domain\HQ\Services\Billing\UsageMeteringService;
use App\Domain\HQ\Services\HQAlertService;
use Illuminate\Support\Facades\Event;
use App\Events\UsageLimitExceeded;

class UsageMeteringTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_record_usage()
    {
        $tenant = HQTenant::create(['name' => 'Test', 'slug' => 'test_tenant_1', 'uuid' => \Illuminate\Support\Str::uuid()]);
        $service = new UsageMeteringService(new EntitlementService(), new HQAlertService());

        $service->recordUsage($tenant, 'storage', 10.5);

        $this->assertDatabaseHas('hq_usage_records', [
            'tenant_id' => $tenant->id,
            'metric_name' => 'storage',
            'value' => '10.5'
        ]);

        $service->recordUsage($tenant, 'storage', 5.0);

        $this->assertDatabaseHas('hq_usage_records', [
            'tenant_id' => $tenant->id,
            'metric_name' => 'storage',
            'value' => '15.5'
        ]);
    }

    public function test_dispatches_event_on_limit_breach()
    {
        Event::fake([\App\Events\UsageLimitExceeded::class]);

        $tenant = HQTenant::create(['name' => 'Test', 'slug' => 'test_tenant_2', 'uuid' => \Illuminate\Support\Str::uuid()]);
        $plan = HQPlan::create([
            'name' => 'Basic',
            'slug' => 'basic',
            'uuid' => \Illuminate\Support\Str::uuid(),
            'limits' => ['storage' => 100],
        ]);

        $subscriptionService = new SubscriptionService(new EntitlementService(), new HQAuditService());
        $subscriptionService->subscribe($tenant, $plan);

        $service = new UsageMeteringService(new EntitlementService(), new HQAlertService());
        
        $service->recordUsage($tenant, 'storage', 90);
        Event::assertNotDispatched(UsageLimitExceeded::class);

        $service->recordUsage($tenant, 'storage', 20); // total 110, limit 100
        Event::assertDispatched(UsageLimitExceeded::class);
    }
}
