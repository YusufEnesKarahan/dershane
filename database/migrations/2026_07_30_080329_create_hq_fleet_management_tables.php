<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hq_release_channels', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });

        Schema::create('hq_instance_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::table('hq_tenants', function (Blueprint $table) {
            $table->foreignId('hq_release_channel_id')->nullable()->constrained('hq_release_channels')->nullOnDelete();
            $table->foreignId('hq_instance_group_id')->nullable()->constrained('hq_instance_groups')->nullOnDelete();
        });

        Schema::create('hq_deployments', function (Blueprint $table) {
            $table->id();
            $table->string('version');
            $table->string('type')->default('manual'); // manual, rolling, canary, blue-green, staged
            $table->string('status')->default('queued'); // queued, running, paused, completed, failed, rollback
            $table->integer('rollout_percentage')->default(0); // 0, 5, 10, 25, 50, 100
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('hq_deployment_targets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hq_deployment_id')->constrained('hq_deployments')->cascadeOnDelete();
            $table->morphs('targetable'); // Can target HQTenant, HQInstanceGroup, or HQSystemInstance
            $table->string('status')->default('pending'); // pending, running, completed, failed, rolled_back
            $table->text('error_message')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('hq_deployment_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hq_deployment_id')->constrained('hq_deployments')->cascadeOnDelete();
            $table->foreignId('hq_system_instance_id')->nullable()->constrained('hq_system_instances')->nullOnDelete();
            $table->string('level')->default('info');
            $table->text('message');
            $table->json('context')->nullable();
            $table->timestamps();
        });

        Schema::create('hq_maintenance_windows', function (Blueprint $table) {
            $table->id();
            $table->morphs('targetable'); // HQTenant or HQInstanceGroup
            $table->timestamp('starts_at');
            $table->timestamp('ends_at');
            $table->string('reason')->nullable();
            $table->boolean('auto_manage')->default(true);
            $table->string('status')->default('scheduled'); // scheduled, active, completed, cancelled
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hq_maintenance_windows');
        Schema::dropIfExists('hq_deployment_logs');
        Schema::dropIfExists('hq_deployment_targets');
        Schema::dropIfExists('hq_deployments');
        
        Schema::table('hq_tenants', function (Blueprint $table) {
            $table->dropForeign(['hq_release_channel_id']);
            $table->dropForeign(['hq_instance_group_id']);
            $table->dropColumn(['hq_release_channel_id', 'hq_instance_group_id']);
        });

        Schema::dropIfExists('hq_instance_groups');
        Schema::dropIfExists('hq_release_channels');
    }
};
