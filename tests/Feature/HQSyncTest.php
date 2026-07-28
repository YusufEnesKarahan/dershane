<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\HQSyncEvent;
use App\Domain\Platform\Services\HQSyncService;

class HQSyncTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);
    }

    public function test_queue_event()
    {
        $service = app(HQSyncService::class);
        $event = $service->queueLicenseChanged(['status' => 'active']);

        $this->assertDatabaseHas('hq_sync_queue', [
            'id' => $event->id,
            'event_type' => 'license.changed',
            'status' => 'pending',
            'retry_count' => 0
        ]);

        $this->assertEquals(1, $service->pending());
        $this->assertEquals(0, $service->completed());
        $this->assertEquals(0, $service->failed());
    }

    public function test_retry_counter()
    {
        $service = app(HQSyncService::class);
        $event = $service->queue('dummy.event');
        $event->update(['status' => 'failed']);

        $this->assertEquals(1, $service->failed());

        $result = $service->retry($event->id);

        $this->assertTrue($result);
        
        $event->refresh();
        $this->assertEquals('pending', $event->status);
        $this->assertEquals(1, $event->retry_count);
        $this->assertNull($event->last_error);
    }

    public function test_payload_builder()
    {
        $service = app(HQSyncService::class);
        $payload = $service->buildPayload('test.event', ['key' => 'value']);

        $this->assertArrayHasKey('event', $payload);
        $this->assertArrayHasKey('timestamp', $payload);
        $this->assertArrayHasKey('data', $payload);
        
        $this->assertEquals('test.event', $payload['event']);
        $this->assertEquals('value', $payload['data']['key']);
    }

    public function test_admin_sync_page()
    {
        config(['app.installed' => true]);

        $superAdmin = User::factory()->create();
        $superAdmin->roles()->attach(Role::where('name', 'Super Admin')->first()->id);
        
        $service = app(HQSyncService::class);
        $service->queue('test.event.admin');

        $response = $this->actingAs($superAdmin)->get('/admin/platform/sync');
        
        $response->assertStatus(200);
        $response->assertViewIs('admin.platform.sync.index');
        $response->assertViewHas('metrics');
        $response->assertViewHas('events');
    }

    public function test_dashboard_widget()
    {
        config(['app.installed' => true]);

        $superAdmin = User::factory()->create();
        $superAdmin->roles()->attach(Role::where('name', 'Super Admin')->first()->id);
        
        $service = app(HQSyncService::class);
        $service->queue('dashboard.event');

        $response = $this->actingAs($superAdmin)->get('/admin/reporting/dashboard');
        
        $response->assertStatus(200);
        
        $content = $response->getContent();
        $this->assertStringContainsString('HQ Sync Queue', $content);
    }
}
