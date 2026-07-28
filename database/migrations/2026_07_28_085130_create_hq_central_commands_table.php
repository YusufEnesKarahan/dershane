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
        Schema::create('hq_central_commands', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('system_instance_id');
            $table->string('command_type');
            $table->json('payload')->nullable();
            $table->enum('status', ['pending', 'approved', 'sent', 'completed', 'failed'])->default('pending');
            $table->timestamps();

            $table->foreign('system_instance_id')->references('id')->on('hq_system_instances')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hq_central_commands');
    }
};
