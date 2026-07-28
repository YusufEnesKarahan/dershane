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
        Schema::create('hq_updates', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('version');
            $table->string('channel')->default('stable');
            $table->string('package_url')->nullable();
            $table->string('checksum')->nullable();
            $table->enum('status', ['available', 'downloaded', 'installed', 'failed'])->default('available');
            $table->timestamp('released_at')->nullable();
            $table->timestamp('installed_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hq_updates');
    }
};
