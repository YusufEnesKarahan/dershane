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
        Schema::create('hq_backup_jobs', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('backup_policy_id')->constrained('hq_backup_policies')->cascadeOnDelete();
            $table->foreignId('system_instance_id')->constrained('hq_system_instances')->cascadeOnDelete();
            $table->enum('status', ['pending', 'running', 'completed', 'failed'])->default('pending');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->bigInteger('size')->nullable(); // Size in bytes
            $table->string('storage_location')->nullable();
            $table->text('error_message')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('h_q_backup_jobs');
    }
};
