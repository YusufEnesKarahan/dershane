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
        Schema::create('hq_backup_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('backup_job_id')->constrained('hq_backup_jobs')->cascadeOnDelete();
            $table->string('action');
            $table->json('payload')->nullable();
            $table->json('response')->nullable();
            $table->timestamp('created_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('h_q_backup_logs');
    }
};
