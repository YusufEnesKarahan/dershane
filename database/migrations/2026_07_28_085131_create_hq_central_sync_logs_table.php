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
        Schema::create('hq_central_sync_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('system_instance_id');
            $table->string('event');
            $table->json('payload')->nullable();
            $table->json('response')->nullable();
            $table->string('status')->default('success');
            $table->integer('duration_ms')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('system_instance_id')->references('id')->on('hq_system_instances')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hq_central_sync_logs');
    }
};
