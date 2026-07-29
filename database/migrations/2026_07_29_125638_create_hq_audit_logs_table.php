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
        Schema::create('hq_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('tenant_id')->nullable()->constrained('hq_tenants')->nullOnDelete();
            $table->foreignId('system_instance_id')->nullable()->constrained('hq_system_instances')->nullOnDelete();
            
            $table->string('action');
            $table->string('category');
            $table->string('severity')->default('info'); // info, warning, danger, critical
            $table->text('description')->nullable();
            
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            
            $table->json('metadata')->nullable();
            
            $table->timestamp('created_at')->nullable();

            $table->index('action');
            $table->index('category');
            $table->index('severity');
            $table->index('tenant_id');
            $table->index('system_instance_id');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hq_audit_logs');
    }
};
