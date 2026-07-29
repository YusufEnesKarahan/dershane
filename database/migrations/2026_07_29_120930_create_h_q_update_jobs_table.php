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
        Schema::create('hq_update_jobs', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('version_id')->constrained('hq_versions')->cascadeOnDelete();
            $table->foreignId('system_instance_id')->nullable()->constrained('hq_system_instances')->nullOnDelete();
            $table->foreignId('tenant_id')->nullable()->constrained('hq_tenants')->nullOnDelete();
            $table->string('target_type'); // single, tenant, global
            $table->string('status')->default('pending'); // pending, scheduled, sent, completed, failed, cancelled
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->integer('progress')->default(0);
            $table->json('result')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('h_q_update_jobs');
    }
};
