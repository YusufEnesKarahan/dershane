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
        // 1. Schedule Slots
        Schema::create('schedule_slots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches')->cascadeOnDelete();
            $table->string('name'); // e.g. "1. Ders", "Sabah Etüdü"
            $table->time('start_time');
            $table->time('end_time');
            $table->timestamps();
        });

        // 2. Lesson Schedules
        Schema::create('lesson_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches')->cascadeOnDelete();
            $table->foreignId('academic_term_id')->constrained('academic_terms')->cascadeOnDelete();
            $table->foreignId('classroom_id')->constrained('classrooms')->cascadeOnDelete();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->foreignId('teacher_id')->nullable()->constrained('teachers')->nullOnDelete();
            
            $table->string('day_of_week'); // Monday, Tuesday, etc.
            $table->time('start_time');
            $table->time('end_time');
            
            $table->string('room')->nullable(); // Derslik/Salon adı
            $table->string('status')->default('active'); // active, cancelled
            
            $table->timestamps();
            $table->softDeletes();
        });

        // 3. Lesson Schedule Teachers (pivot for additional teachers)
        Schema::create('lesson_schedule_teachers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lesson_schedule_id')->constrained('lesson_schedules')->cascadeOnDelete();
            $table->foreignId('teacher_id')->constrained('teachers')->cascadeOnDelete();
            $table->timestamps();
            
            $table->unique(['lesson_schedule_id', 'teacher_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lesson_schedule_teachers');
        Schema::dropIfExists('lesson_schedules');
        Schema::dropIfExists('schedule_slots');
    }
};
