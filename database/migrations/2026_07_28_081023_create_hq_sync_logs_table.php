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
        Schema::create('hq_sync_logs', function (Blueprint $table) {
            $table->id();
            $table->string('event_type');
            $table->string('request_url');
            $table->string('request_method');
            $table->json('request_payload')->nullable();
            $table->integer('response_status')->nullable();
            $table->json('response_payload')->nullable();
            $table->integer('duration_ms')->default(0);
            $table->boolean('success')->default(false);
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hq_sync_logs');
    }
};
