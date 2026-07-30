<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\HQTenant;
use App\Models\HQSystemInstance;
use App\Models\HQBackupStorageLocation;
use App\Models\HQBackupPolicy;
use App\Models\HQBackupJob;
use App\Models\HQBackupSnapshot;
use App\Models\HQBackupRestoreJob;
use App\Models\HQDisasterRecoveryPlan;
use App\Models\HQBackupRetentionRule;
use App\Domain\HQ\Services\Backup\BackupOrchestrationService;
use App\Domain\HQ\Services\Backup\RestoreService;
use App\Domain\HQ\Services\Backup\RetentionService;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;
use App\Jobs\ProcessBackupJob;
use App\Jobs\ProcessRestoreJob;

class BackupOrchestrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        $mockEntitlement = \Mockery::mock(\App\Domain\HQ\Services\HQEntitlementService::class);
        $mockEntitlement->shouldReceive('checkQuota')->andReturn(true);
        $this->app->instance(\App\Domain\HQ\Services\HQEntitlementService::class, $mockEntitlement);
    }

    public function test_it_creates_storage_location()
    {
        $storage = HQBackupStorageLocation::create([
            'name' => 'S3 Backup Storage',
            'driver' => 's3',
            'credentials' => ['key' => '123', 'secret' => '456'],
            'capacity_bytes' => 104857600, // 100MB
            'uuid' => 'usx'
        ]);

        $this->assertDatabaseHas('hq_backup_storage_locations', ['name' => 'S3 Backup Storage', 'driver' => 's3']);
        $this->assertEquals(104857600, $storage->capacity_bytes);
    }

    public function test_it_orchestrates_a_backup_job()
    {
        Queue::fake();
        Event::fake([\App\Events\BackupCompleted::class, \App\Events\RestoreCompleted::class, \App\Events\RestoreFailed::class]);

        $tenant = HQTenant::create(['name' => 'Test Tenant', 'domain' => 'test.com', 'slug' => 'test-tenant', 'uuid' => 'u123']);
        $instance = HQSystemInstance::create(['tenant_id' => $tenant->id, 'system_name' => 'DB1', 'type' => 'database', 'system_uuid' => 'su123', 'system_version' => '1.0']);
        $storage = HQBackupStorageLocation::create(['name' => 'S3', 'driver' => 's3', 'uuid' => 'us1']);
        
        $policy = HQBackupPolicy::create([
            'tenant_id' => $tenant->id,
            'system_instance_id' => $instance->id,
            'hq_backup_storage_location_id' => $storage->id,
            'name' => 'Daily DB Backup',
            'frequency' => 'daily',
            'backup_type' => 'full',
            'retention_days' => 7,
            'uuid' => 'up1'
        ]);

        $service = app(BackupOrchestrationService::class);
        $job = $service->startBackup($policy);

        $this->assertDatabaseHas('hq_backup_jobs', [
            'backup_policy_id' => $policy->id,
            'status' => 'pending'
        ]);

        Queue::assertPushed(ProcessBackupJob::class);
    }

    public function test_it_completes_a_backup_job_and_creates_snapshot()
    {
        Event::fake([\App\Events\BackupCompleted::class, \App\Events\RestoreCompleted::class, \App\Events\RestoreFailed::class]);

        $tenant = HQTenant::create(['name' => 'Test Tenant', 'domain' => 'test.com', 'slug' => 'test-tenant2', 'uuid' => 'u124']);
        $instance = HQSystemInstance::create(['tenant_id' => $tenant->id, 'system_name' => 'DB1', 'type' => 'database', 'system_uuid' => 'su124', 'system_version' => '1.0']);
        $storage = HQBackupStorageLocation::create(['name' => 'S3', 'driver' => 's3', 'uuid' => 'us2']);
        $policy = HQBackupPolicy::create([
            'tenant_id' => $tenant->id,
            'system_instance_id' => $instance->id,
            'hq_backup_storage_location_id' => $storage->id,
            'name' => 'Test Policy',
            'frequency' => 'daily',
            'backup_type' => 'full',
            'retention_days' => 7,
            'uuid' => 'up2'
        ]);

        $job = HQBackupJob::create([
            'backup_policy_id' => $policy->id,
            'system_instance_id' => $instance->id,
            'status' => 'running',
            'storage_location' => 'S3',
            'uuid' => 'uj1'
        ]);

        $service = app(BackupOrchestrationService::class);
        $service->completeBackup($job, 5000, '/path/to/backup.zip', 'full');

        $this->assertDatabaseHas('hq_backup_jobs', ['id' => $job->id, 'status' => 'completed', 'size' => 5000]);
        $this->assertDatabaseHas('hq_backup_snapshots', ['hq_backup_job_id' => $job->id, 'path' => '/path/to/backup.zip', 'size_bytes' => 5000]);
        
        // Assert storage usage incremented
        $this->assertEquals(5000, $storage->fresh()->used_bytes);

        Event::assertDispatched(\App\Events\BackupCompleted::class);
    }

    public function test_retention_rules_pruning()
    {
        $tenant = HQTenant::create(['name' => 'Test', 'domain' => 'test.com', 'slug' => 'test-tenant3', 'uuid' => 'u125']);
        $instance = HQSystemInstance::create(['tenant_id' => $tenant->id, 'system_name' => 'DB1', 'type' => 'database', 'system_uuid' => 'su125', 'system_version' => '1.0']);
        $storage = HQBackupStorageLocation::create(['name' => 'S3', 'driver' => 's3', 'used_bytes' => 10000, 'uuid' => 'us3']);
        $policy = HQBackupPolicy::create(['tenant_id' => $tenant->id, 'system_instance_id' => $instance->id, 'hq_backup_storage_location_id' => $storage->id, 'name' => 'Test', 'frequency' => 'daily', 'backup_type' => 'full', 'retention_days' => 7, 'uuid' => 'up3']);
        
        $job = HQBackupJob::create(['backup_policy_id' => $policy->id, 'system_instance_id' => $instance->id, 'status' => 'completed', 'uuid' => 'uj2']);
        
        $snapshot = HQBackupSnapshot::create([
            'hq_backup_job_id' => $job->id,
            'type' => 'full',
            'path' => '/a',
            'size_bytes' => 10000,
            'expires_at' => now()->subDay() // Expired yesterday
        ]);

        app(RetentionService::class)->pruneExpiredSnapshots();

        $this->assertDatabaseMissing('hq_backup_snapshots', ['id' => $snapshot->id]);
        $this->assertEquals(0, $storage->fresh()->used_bytes);
    }

    public function test_it_orchestrates_a_restore_job()
    {
        Queue::fake();

        $tenant = HQTenant::create(['name' => 'Test', 'domain' => 'test.com', 'slug' => 'test-tenant4', 'uuid' => 'u126']);
        $instance = HQSystemInstance::create(['tenant_id' => $tenant->id, 'system_name' => 'DB1', 'type' => 'database', 'system_uuid' => 'su126', 'system_version' => '1.0']);
        $policy = HQBackupPolicy::create(['tenant_id' => $tenant->id, 'system_instance_id' => $instance->id, 'name' => 'Test Policy', 'frequency' => 'daily', 'backup_type' => 'full', 'retention_days' => 7, 'uuid' => 'up4']);
        $job = HQBackupJob::create(['backup_policy_id' => $policy->id, 'system_instance_id' => $instance->id, 'status' => 'completed', 'uuid' => 'uj3']);
        $snapshot = HQBackupSnapshot::create(['hq_backup_job_id' => $job->id, 'type' => 'full', 'path' => '/a', 'size_bytes' => 10]);

        // Mock HQAuditService to avoid severity null issue
        $mockAudit = \Mockery::mock(\App\Domain\HQ\Services\HQAuditService::class);
        $mockAudit->shouldReceive('logSystemAction')->andReturn(new \App\Models\HQAuditLog());
        $this->app->instance(\App\Domain\HQ\Services\HQAuditService::class, $mockAudit);

        $service = app(RestoreService::class);
        $restore = $service->startRestore($snapshot, $instance, 'dry_run', 'latest');

        $this->assertDatabaseHas('hq_backup_restore_jobs', [
            'target_instance_id' => $instance->id,
            'hq_backup_snapshot_id' => $snapshot->id,
            'mode' => 'dry_run'
        ]);

        Queue::assertPushed(ProcessRestoreJob::class);
    }

    public function test_it_evaluates_entitlement_limits()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Backup failed: Storage quota exceeded.");

        // Simulate quota exception
        $tenant = HQTenant::create(['name' => 'Test Tenant', 'domain' => 'test.com', 'slug' => 'test-tenant']);
        $storage = HQBackupStorageLocation::create(['name' => 'S3', 'driver' => 's3', 'used_bytes' => 99999999999999]); // Very high usage
        $policy = HQBackupPolicy::create([
            'tenant_id' => $tenant->id,
            'hq_backup_storage_location_id' => $storage->id,
            'name' => 'Test Policy',
            'frequency' => 'daily',
            'backup_type' => 'full',
            'retention_days' => 7,
        ]);

        // Setup mock entitlement
        $mock = \Mockery::mock(\App\Domain\HQ\Services\HQEntitlementService::class);
        $mock->shouldReceive('checkQuota')->andReturn(false);
        $this->app->instance(\App\Domain\HQ\Services\HQEntitlementService::class, $mock);

        $service = app(BackupOrchestrationService::class);
        $service->startBackup($policy);
    }
}
