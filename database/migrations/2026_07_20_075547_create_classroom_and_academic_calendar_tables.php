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

        Schema::dropIfExists('holidays');
        Schema::dropIfExists('schedule_exceptions');
        Schema::dropIfExists('class_schedules');
        Schema::dropIfExists('academic_weeks');
        Schema::dropIfExists('academic_terms');
        Schema::dropIfExists('classroom_capacities');
        Schema::dropIfExists('classrooms');
        Schema::dropIfExists('classroom_types');

        Schema::enableForeignKeyConstraints();

        Schema::create('classroom_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('classrooms', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->foreignId('classroom_type_id')->nullable()->constrained('classroom_types')->nullOnDelete();
            $table->integer('capacity')->default(30);
            $table->string('color_code')->default('#4F46E5');
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('classroom_capacities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('classroom_id')->constrained('classrooms')->cascadeOnDelete();
            $table->integer('max_capacity');
            $table->integer('exam_capacity')->nullable();
            $table->date('effective_date')->nullable();
            $table->timestamps();
        });

        Schema::create('academic_terms', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('academic_weeks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_term_id')->constrained('academic_terms')->cascadeOnDelete();
            $table->integer('week_number');
            $table->date('start_date');
            $table->date('end_date');
            $table->timestamps();
        });

        Schema::create('class_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('classroom_id')->constrained('classrooms')->cascadeOnDelete();
            $table->foreignId('teacher_id')->constrained('teachers')->cascadeOnDelete();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->foreignId('academic_term_id')->nullable()->constrained('academic_terms')->nullOnDelete();
            $table->unsignedTinyInteger('day_of_week'); // 1 = Pazartesi ... 7 = Pazar
            $table->time('start_time');
            $table->time('end_time');
            $table->string('color_code')->default('#3B82F6');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('schedule_exceptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_schedule_id')->constrained('class_schedules')->cascadeOnDelete();
            $table->date('exception_date');
            $table->string('type')->default('Cancellation'); // Cancellation, MakeUp
            $table->date('make_up_date')->nullable();
            $table->time('make_up_start_time')->nullable();
            $table->time('make_up_end_time')->nullable();
            $table->text('reason')->nullable();
            $table->timestamps();
        });

        Schema::create('holidays', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->date('start_date');
            $table->date('end_date');
            $table->foreignId('branch_id')->nullable()->constrained('branches')->cascadeOnDelete();
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();

        Schema::dropIfExists('holidays');
        Schema::dropIfExists('schedule_exceptions');
        Schema::dropIfExists('class_schedules');
        Schema::dropIfExists('academic_weeks');
        Schema::dropIfExists('academic_terms');
        Schema::dropIfExists('classroom_capacities');
        Schema::dropIfExists('classrooms');
        Schema::dropIfExists('classroom_types');

        Schema::enableForeignKeyConstraints();
    }
};
