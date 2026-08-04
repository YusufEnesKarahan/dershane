<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('attendance_excuses');
        Schema::dropIfExists('attendances');
        Schema::dropIfExists('attendance_sessions');
        Schema::dropIfExists('attendance_statuses');
        Schema::dropIfExists('attendance_settings');
        Schema::dropIfExists('attendance_records');
        Schema::enableForeignKeyConstraints();

        Schema::create('attendance_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches')->onDelete('cascade');
            $table->foreignId('classroom_id')->constrained('classrooms')->onDelete('cascade');
            $table->foreignId('lesson_schedule_id')->nullable()->constrained('lesson_schedules')->onDelete('set null');
            $table->foreignId('teacher_id')->constrained('teachers')->onDelete('cascade');
            $table->date('session_date');
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->string('status')->default('open'); // open, completed, cancelled
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['branch_id', 'session_date']);
        });

        Schema::create('attendance_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches')->onDelete('cascade');
            $table->foreignId('attendance_session_id')->nullable()->constrained('attendance_sessions')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('classroom_id')->constrained('classrooms')->onDelete('cascade');
            $table->foreignId('lesson_schedule_id')->nullable()->constrained('lesson_schedules')->onDelete('set null');
            $table->foreignId('teacher_id')->constrained('teachers')->onDelete('cascade');
            $table->date('attendance_date');
            $table->string('status'); // present, absent, late, excused
            $table->text('note')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['branch_id', 'attendance_date']);
            $table->index(['student_id', 'attendance_date']);
            $table->index(['classroom_id', 'attendance_date']);
        });

        Schema::create('attendance_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches')->onDelete('cascade');
            $table->integer('late_threshold_minutes')->default(15);
            $table->boolean('require_note_for_absence')->default(false);
            $table->boolean('auto_create_from_schedule')->default(false);
            $table->timestamps();
            
            $table->unique('branch_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_settings');
        Schema::dropIfExists('attendance_records');
        Schema::dropIfExists('attendance_sessions');
    }
};
