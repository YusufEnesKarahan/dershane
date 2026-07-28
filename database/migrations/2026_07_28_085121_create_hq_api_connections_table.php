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
        Schema::create('hq_api_connections', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('system_instance_id');
            $table->string('token_hash')->nullable();
            $table->timestamp('last_request_at')->nullable();
            $table->ipAddress('ip_address')->nullable();
            $table->string('status')->default('success');
            $table->timestamps();

            $table->foreign('system_instance_id')->references('id')->on('hq_system_instances')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hq_api_connections');
    }
};
