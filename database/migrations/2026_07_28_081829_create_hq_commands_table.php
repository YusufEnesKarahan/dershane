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
        Schema::create('hq_commands', function (Blueprint $table) {
            $table->id();
            $table->uuid('command_uuid')->unique();
            $table->string('command_type');
            $table->json('payload')->nullable();
            $table->enum('status', ['pending', 'approved', 'executed', 'failed', 'rejected'])->default('pending');
            $table->json('result')->nullable();
            $table->timestamp('requested_at')->useCurrent();
            $table->timestamp('executed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hq_commands');
    }
};
