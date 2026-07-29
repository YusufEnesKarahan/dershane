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
        Schema::create('hq_configuration_profiles', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name');
            $table->enum('scope', ['global', 'tenant', 'instance']);
            $table->foreignId('tenant_id')->nullable()->constrained('hq_tenants')->cascadeOnDelete();
            $table->foreignId('system_instance_id')->nullable()->constrained('hq_system_instances')->cascadeOnDelete();
            $table->string('environment')->nullable(); // e.g. production, staging
            $table->text('description')->nullable();
            $table->string('status')->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('h_q_configuration_profiles');
    }
};
