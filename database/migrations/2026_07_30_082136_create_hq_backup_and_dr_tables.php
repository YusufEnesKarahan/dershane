<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Storage Locations
        Schema::create('hq_backup_storage_locations', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name');
            $table->string('driver'); // local, s3, azure_blob, ftp, sftp, minio
            $table->text('credentials')->nullable(); // Encrypted JSON
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('capacity_bytes')->nullable();
            $table->unsignedBigInteger('used_bytes')->default(0);
            $table->timestamps();
        });

        // Add storage_location_id to existing hq_backup_policies
        Schema::table('hq_backup_policies', function (Blueprint $table) {
            if (!Schema::hasColumn('hq_backup_policies', 'hq_backup_storage_location_id')) {
                $table->foreignId('hq_backup_storage_location_id')->nullable()->constrained('hq_backup_storage_locations')->nullOnDelete();
            }
        });

        // 2. Retention Rules
        Schema::create('hq_backup_retention_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hq_backup_policy_id')->constrained('hq_backup_policies')->cascadeOnDelete();
            $table->string('rule_type'); // 24_hour, 7_day, 30_day, 90_day, 365_day, keep_forever
            $table->timestamps();
        });

        // 3. Snapshots
        Schema::create('hq_backup_snapshots', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('hq_backup_job_id')->constrained('hq_backup_jobs')->cascadeOnDelete();
            $table->string('type'); // full, incremental, differential, metadata
            $table->string('path');
            $table->unsignedBigInteger('size_bytes')->default(0);
            $table->string('checksum')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });

        // 4. Restore Jobs
        Schema::create('hq_backup_restore_jobs', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('target_instance_id')->constrained('hq_system_instances')->cascadeOnDelete();
            $table->foreignId('hq_backup_snapshot_id')->constrained('hq_backup_snapshots')->cascadeOnDelete();
            $table->string('type'); // latest, specific, point_in_time
            $table->string('mode'); // dry_run, validation, execute
            $table->string('status')->default('pending'); // pending, running, completed, failed
            $table->text('error_message')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });

        // 5. Disaster Recovery Plans
        Schema::create('hq_disaster_recovery_plans', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('tenant_id')->constrained('hq_tenants')->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('priority')->default('medium'); // high, medium, low
            $table->json('dependencies')->nullable(); // JSON array of other DR plans or components
            $table->string('status')->default('active'); // active, inactive, testing, running
            $table->timestamp('last_run_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hq_disaster_recovery_plans');
        Schema::dropIfExists('hq_backup_restore_jobs');
        Schema::dropIfExists('hq_backup_snapshots');
        Schema::dropIfExists('hq_backup_retention_rules');
        
        Schema::table('hq_backup_policies', function (Blueprint $table) {
            $table->dropForeign(['hq_backup_storage_location_id']);
            $table->dropColumn('hq_backup_storage_location_id');
        });
        
        Schema::dropIfExists('hq_backup_storage_locations');
    }
};
