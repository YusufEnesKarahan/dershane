<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\HQTenant;
use App\Models\HQSystemInstance;
use App\Models\HQAlertRule;
use App\Models\HQAlert;
use App\Events\SystemOfflineDetected;
use Illuminate\Support\Facades\Event;

class HQAlertTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Mock authorization for Super Admin
        $this->user = User::factory()->create();
        $this->user->assignRole('Super Admin');
        
        $this->tenant = HQTenant::create([
            'name' => 'Test Tenant',
            'status' => 'active',
        ]);
        
        $this->instance = HQSystemInstance::create([
            'tenant_id' => $this->tenant->id,
            'system_uuid' => 'sys-1234',
            'system_name' => 'Test System',
            'status' => 'online',
            'last_seen_at' => now(),
        ]);
    }

    public function test_alert_rule_evaluates_system_offline_event()
    {
        $rule = HQAlertRule::create([
            'name' => 'Offline System Alert',
            'category' => 'system',
            'severity' => 'critical',
            'event_type' => 'system.offline',
            'condition' => ['type' => 'system_offline'],
            'is_active' => true,
        ]);

        event(new SystemOfflineDetected($this->instance, 20));

        $this->assertDatabaseHas('hq_alerts', [
            'rule_id' => $rule->id,
            'system_instance_id' => $this->instance->id,
            'severity' => 'critical',
            'status' => 'open',
        ]);

        $this->assertDatabaseHas('hq_notification_logs', [
            'channel' => 'database',
            'status' => 'sent',
        ]);
    }

    public function test_api_can_report_alert()
    {
        $payload = [
            'system_id' => 'sys-1234',
            'type' => 'security',
            'message' => 'Multiple failed login attempts',
            'severity' => 'danger',
            'metadata' => ['ip' => '192.168.1.1'],
        ];

        // Bypass VerifyHQApiSignature middleware for testing
        $this->withoutMiddleware(\App\Http\Middleware\VerifyHQApiSignature::class);

        $response = $this->postJson('/api/hq/alerts/report', $payload);

        $response->assertStatus(200);
        $response->assertJson(['status' => 'success']);
    }

    public function test_super_admin_can_view_alerts()
    {
        $alert = HQAlert::create([
            'title' => 'Test Alert',
            'message' => 'This is a test',
            'severity' => 'warning',
            'status' => 'open',
            'tenant_id' => $this->tenant->id,
        ]);

        $response = $this->actingAs($this->user)->get('/admin/hq-central/alerts');
        $response->assertStatus(200);
        $response->assertSee('Test Alert');
    }

    public function test_super_admin_can_acknowledge_alert()
    {
        $alert = HQAlert::create([
            'title' => 'Test Alert',
            'message' => 'This is a test',
            'severity' => 'warning',
            'status' => 'open',
        ]);

        $response = $this->actingAs($this->user)->post("/admin/hq-central/alerts/{$alert->id}/acknowledge");
        $response->assertRedirect();
        
        $this->assertDatabaseHas('hq_alerts', [
            'id' => $alert->id,
            'status' => 'acknowledged',
        ]);
    }
}
