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
        Schema::create('hq_backup_policies', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('tenant_id')->nullable()->constrained('hq_tenants')->cascadeOnDelete();
            $table->foreignId('system_instance_id')->nullable()->constrained('hq_system_instances')->cascadeOnDelete();
            $table->string('name');
            $table->enum('frequency', ['daily', 'weekly', 'monthly']);
            $table->integer('retention_days')->default(7);
            $table->enum('backup_type', ['database', 'files', 'full']);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('h_q_backup_policies');
    }
};
