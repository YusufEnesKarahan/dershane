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
        Schema::create('license_cache', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('system_uuid');
            $table->string('license_key')->nullable();
            $table->string('status')->default('unknown');
            $table->string('plan')->nullable();
            $table->json('features')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('last_checked_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('license_cache');
    }
};
