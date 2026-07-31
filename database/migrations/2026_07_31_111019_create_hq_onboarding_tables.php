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
        Schema::dropIfExists('hq_provisioning_tasks');
        Schema::dropIfExists('hq_tenant_invitations');
        Schema::dropIfExists('hq_onboarding_flows');

        Schema::create('hq_onboarding_flows', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('tenant_id')->nullable()->constrained('hq_tenants')->onDelete('cascade');
            $table->string('current_step');
            $table->string('status')->default('pending'); // pending, in_progress, completed, failed
            $table->json('metadata')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('hq_tenant_invitations', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('tenant_id')->constrained('hq_tenants')->onDelete('cascade');
            $table->string('email');
            $table->foreignId('role_id')->nullable()->constrained('roles')->onDelete('set null');
            $table->string('token_hash')->unique();
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('accepted_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('hq_provisioning_tasks', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('tenant_id')->constrained('hq_tenants')->onDelete('cascade');
            $table->string('task_type');
            $table->string('status')->default('pending'); // pending, processing, completed, failed
            $table->json('payload')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hq_provisioning_tasks');
        Schema::dropIfExists('hq_tenant_invitations');
        Schema::dropIfExists('hq_onboarding_flows');
    }
};
