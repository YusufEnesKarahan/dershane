<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\HQTenant;
use App\Models\PortalNotification;
use App\Domain\Portal\Services\NotificationCenterService;

class PortalTenantIsolationTest extends TestCase
{
    use RefreshDatabase;

    public function test_tenant_cannot_see_other_tenant_notifications()
    {
        $tenant1 = HQTenant::create(['name' => 'Tenant 1', 'slug' => 'tenant-1', 'uuid' => \Illuminate\Support\Str::uuid()]);
        $tenant2 = HQTenant::create(['name' => 'Tenant 2', 'slug' => 'tenant-2', 'uuid' => \Illuminate\Support\Str::uuid()]);

        $service = app(NotificationCenterService::class);
        
        $service->sendNotification($tenant1, 'Message 1', 'Message for T1');
        $service->sendNotification($tenant2, 'Message 2', 'Message for T2');

        $t1Notifications = $service->getNotifications($tenant1);
        
        $this->assertCount(1, $t1Notifications);
        $this->assertEquals('Message 1', $t1Notifications->first()->title);
    }
}
