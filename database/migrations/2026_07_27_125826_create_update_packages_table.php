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
        Schema::create('update_packages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('version');
            $table->string('build')->nullable();
            $table->text('description')->nullable();
            $table->string('checksum')->nullable();
            $table->boolean('is_mandatory')->default(false);
            $table->timestamp('release_date')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('update_packages');
    }
};
