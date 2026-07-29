<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\HQBackupPolicy;
use App\Models\HQBackupJob;
use App\Models\HQBackupLog;
use App\Models\HQTenant;
use App\Models\HQSystemInstance;
use App\Domain\HQ\Services\HQBackupService;

class HQBackupTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $tenant;
    protected $instance;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);
        $this->admin = User::factory()->create();
        $this->admin->roles()->attach(Role::where('name', 'Super Admin')->first()->id);

        config(['app.installed' => true]);
        config(['hq.api.token' => 'test-hq-token']);
        config(['hq.api.secret' => 'test-secret']);

        $this->tenant = HQTenant::create(['name' => 'Test Tenant', 'slug' => 'test-tenant']);
        $this->instance = HQSystemInstance::create([
            'tenant_id' => $this->tenant->id,
            'system_uuid' => 'sys-uuid-test',
            'system_name' => 'Sys1',
            'system_version' => '1.0.0',
            'status' => 'online'
        ]);
    }

    public function test_can_create_backup_policy()
    {
        $response = $this->actingAs($this->admin)->post(route('admin.platform.hq_central.backups.store'), [
            'name' => 'Nightly Backup',
            'frequency' => 'daily',
            'retention_days' => 14,
            'backup_type' => 'full',
            'is_active' => true,
        ]);

        $response->assertRedirect();
        
        $this->assertDatabaseHas('hq_backup_policies', [
            'name' => 'Nightly Backup',
            'retention_days' => 14,
            'backup_type' => 'full'
        ]);
    }

    public function test_unauthorized_user_cannot_manage_backups()
    {
        $user = User::factory()->create();
        // User without roles
        $response = $this->actingAs($user)->post(route('admin.platform.hq_central.backups.store'), [
            'name' => 'Hacked Backup',
            'frequency' => 'daily',
            'retention_days' => 1,
            'backup_type' => 'full',
            'is_active' => true,
        ]);

        $response->assertStatus(403);
    }

    public function test_backup_service_can_dispatch_job_and_log()
    {
        $policy = HQBackupPolicy::create([
            'name' => 'Test Policy',
            'frequency' => 'weekly',
            'retention_days' => 7,
            'backup_type' => 'database',
            'is_active' => true,
        ]);

        $service = app(HQBackupService::class);
        $job = $service->dispatchBackup($this->instance, $policy);

        $this->assertDatabaseHas('hq_backup_jobs', [
            'id' => $job->id,
            'backup_policy_id' => $policy->id,
            'system_instance_id' => $this->instance->id,
            'status' => 'pending'
        ]);

        $this->assertDatabaseHas('hq_backup_logs', [
            'backup_job_id' => $job->id,
            'action' => 'dispatch'
        ]);

        $this->assertDatabaseHas('hq_central_commands', [
            'system_instance_id' => $this->instance->id,
            'command_type' => 'backup_start',
            'status' => 'pending'
        ]);
    }

    public function test_api_requires_hmac_signature()
    {
        $response = $this->postJson('/api/hq/backup/check', []);
        
        $response->assertStatus(401);
    }

    public function test_api_progress_callback()
    {
        $job = HQBackupJob::create([
            'backup_policy_id' => HQBackupPolicy::create([
                'name' => 'P', 'frequency' => 'daily', 'backup_type' => 'full'
            ])->id,
            'system_instance_id' => $this->instance->id,
            'status' => 'running',
        ]);

        $secret = config('hq.api.secret');
        $timestamp = time();
        $payload = [
            'job_id' => $job->id,
            'progress' => 50,
        ];
        $signature = hash_hmac('sha256', json_encode($payload) . $timestamp, $secret);

        $response = $this->postJson('/api/hq/backup/progress', $payload, [
            'Authorization' => 'Bearer ' . config('hq.api.token'),
            'X-HQ-Signature' => $signature,
            'X-HQ-Timestamp' => (string) $timestamp,
        ]);

        $response->assertStatus(200);
        
        $job->refresh();
        $this->assertEquals(50, $job->metadata['progress']);
    }

    public function test_failed_backup_can_be_retried()
    {
        $policy = HQBackupPolicy::create([
            'name' => 'Test Policy',
            'frequency' => 'weekly',
            'retention_days' => 7,
            'backup_type' => 'database',
            'is_active' => true,
        ]);

        $job = HQBackupJob::create([
            'backup_policy_id' => $policy->id,
            'system_instance_id' => $this->instance->id,
            'status' => 'failed',
            'error_message' => 'Connection timeout'
        ]);

        $service = app(HQBackupService::class);
        $retriedJob = $service->retryFailedBackup($job);

        $this->assertEquals('pending', $retriedJob->status);
        $this->assertNull($retriedJob->error_message);
        $this->assertDatabaseHas('hq_backup_logs', [
            'backup_job_id' => $job->id,
            'action' => 'retry'
        ]);
    }

    public function test_retention_cleanup_removes_expired_jobs()
    {
        $policy = HQBackupPolicy::create([
            'name' => 'Test Policy',
            'frequency' => 'daily',
            'retention_days' => 5,
            'backup_type' => 'database',
            'is_active' => true,
        ]);

        $expiredJob = HQBackupJob::create([
            'backup_policy_id' => $policy->id,
            'system_instance_id' => $this->instance->id,
            'status' => 'completed',
            'finished_at' => now()->subDays(6)
        ]);

        $recentJob = HQBackupJob::create([
            'backup_policy_id' => $policy->id,
            'system_instance_id' => $this->instance->id,
            'status' => 'completed',
            'finished_at' => now()->subDays(2)
        ]);

        $service = app(HQBackupService::class);
        $service->cleanupExpiredBackups();

        $this->assertDatabaseMissing('hq_backup_jobs', ['id' => $expiredJob->id]);
        $this->assertDatabaseHas('hq_backup_jobs', ['id' => $recentJob->id]);
    }
}
