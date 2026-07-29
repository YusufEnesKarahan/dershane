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
        Schema::table('hq_central_commands', function (Blueprint $table) {
            $table->integer('priority')->default(0);
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('executed_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->integer('retry_count')->default(0);
            $table->integer('max_retry')->default(3);
            $table->json('response')->nullable();
            $table->text('error_message')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hq_central_commands', function (Blueprint $table) {
            $table->dropColumn([
                'priority',
                'scheduled_at',
                'executed_at',
                'expires_at',
                'retry_count',
                'max_retry',
                'response',
                'error_message'
            ]);
        });
    }
};
