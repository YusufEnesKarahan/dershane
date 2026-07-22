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

        Schema::dropIfExists('teacher_performance_logs');
        Schema::dropIfExists('teacher_assignments');
        Schema::dropIfExists('teacher_subjects');
        Schema::dropIfExists('teacher_profiles');

        Schema::enableForeignKeyConstraints();

        Schema::create('teacher_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained('teachers')->cascadeOnDelete();
            $table->text('bio_extended')->nullable();
            $table->string('office_hours')->nullable();
            $table->string('room_number')->nullable();
            $table->timestamps();
        });

        Schema::create('teacher_subjects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained('teachers')->cascadeOnDelete();
            $table->string('subject_name');
            $table->string('code');
            $table->timestamps();
        });

        Schema::create('teacher_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained('teachers')->cascadeOnDelete();
            $table->foreignId('classroom_id')->constrained('classrooms')->cascadeOnDelete();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::create('teacher_performance_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained('teachers')->cascadeOnDelete();
            $table->string('metric_type'); // Student Success Rate, Attendance Rate, Parent Rating
            $table->decimal('score', 5, 2);
            $table->text('comments')->nullable();
            $table->date('evaluated_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();

        Schema::dropIfExists('teacher_performance_logs');
        Schema::dropIfExists('teacher_assignments');
        Schema::dropIfExists('teacher_subjects');
        Schema::dropIfExists('teacher_profiles');

        Schema::enableForeignKeyConstraints();
    }
};
