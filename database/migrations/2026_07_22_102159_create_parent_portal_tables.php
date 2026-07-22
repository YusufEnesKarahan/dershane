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
        Schema::disableForeignKeyConstraints();

        Schema::dropIfExists('parent_access_logs');
        Schema::dropIfExists('parent_notifications');
        Schema::dropIfExists('parent_devices');
        Schema::dropIfExists('parent_students');

        Schema::enableForeignKeyConstraints();

        Schema::create('parent_students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->string('relation_type')->nullable(); // Mother, Father, Guardian
            $table->timestamps();

            $table->unique(['parent_id', 'student_id']);
        });

        Schema::create('parent_devices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->constrained('users')->cascadeOnDelete();
            $table->string('device_token');
            $table->string('platform')->default('iOS'); // iOS, Android
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('parent_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->text('content');
            $table->boolean('is_read')->default(false);
            $table->timestamps();
        });

        Schema::create('parent_access_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->constrained('users')->cascadeOnDelete();
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->string('action')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();

        Schema::dropIfExists('parent_access_logs');
        Schema::dropIfExists('parent_notifications');
        Schema::dropIfExists('parent_devices');
        Schema::dropIfExists('parent_students');

        Schema::enableForeignKeyConstraints();
    }
};
