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
        Schema::create('hq_telemetry_records', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('system_instance_id');
            $table->string('type')->default('snapshot');
            $table->json('payload')->nullable();
            $table->timestamp('received_at')->useCurrent();
            $table->timestamps();

            $table->foreign('system_instance_id')->references('id')->on('hq_system_instances')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hq_telemetry_records');
    }
};
