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
        // Drop the old settings table
        Schema::dropIfExists('settings');

        Schema::create('setting_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('description')->nullable();
            $table->string('icon')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('platform_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained('setting_groups')->cascadeOnDelete();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('type')->default('text'); // text, textarea, boolean, json, file
            $table->boolean('is_encrypted')->default(false);
            $table->text('validation_rules')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('setting_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('setting_id')->constrained('platform_settings')->cascadeOnDelete();
            $table->text('old_value')->nullable();
            $table->text('new_value')->nullable();
            $table->foreignId('changed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('setting_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('setting_id')->constrained('platform_settings')->cascadeOnDelete();
            $table->string('disk')->default('public');
            $table->string('filename');
            $table->string('original_name');
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('size');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('setting_files');
        Schema::dropIfExists('setting_histories');
        Schema::dropIfExists('platform_settings');
        Schema::dropIfExists('setting_groups');
    }
};
