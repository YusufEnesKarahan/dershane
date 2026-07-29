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
        Schema::create('hq_configuration_logs', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('profile_id')->constrained('hq_configuration_profiles')->cascadeOnDelete();
            $table->foreignId('system_instance_id')->nullable()->constrained('hq_system_instances')->nullOnDelete();
            $table->string('action'); // e.g. create, update, sync, rollback
            $table->string('status');
            $table->json('old_value')->nullable();
            $table->json('new_value')->nullable();
            $table->json('response')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('h_q_configuration_logs');
    }
};
