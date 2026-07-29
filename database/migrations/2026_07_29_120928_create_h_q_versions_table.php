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
        Schema::create('hq_versions', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('version')->unique();
            $table->string('channel')->default('stable');
            $table->text('release_notes')->nullable();
            $table->string('minimum_supported_version')->nullable();
            $table->boolean('is_mandatory')->default(false);
            $table->string('status')->default('draft');
            $table->timestamp('published_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('h_q_versions');
    }
};
