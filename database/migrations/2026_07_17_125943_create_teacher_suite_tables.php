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
        // Drop any dependencies or old table to avoid constraint errors
        Schema::dropIfExists('teacher_contracts');
        Schema::dropIfExists('teacher_salary_profiles');
        Schema::dropIfExists('teacher_leave_requests');
        Schema::dropIfExists('teacher_performance');
        Schema::dropIfExists('teacher_activity_logs');
        Schema::dropIfExists('teacher_notes');
        Schema::dropIfExists('teacher_schedules');
        Schema::dropIfExists('teacher_availability');
        Schema::dropIfExists('teacher_experiences');
        Schema::dropIfExists('teacher_certificates');
        Schema::dropIfExists('teacher_documents');
        Schema::dropIfExists('teachers');

        Schema::create('teachers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title')->nullable();
            $table->text('bio')->nullable();
            $table->text('specialties')->nullable(); // comma-separated or json
            $table->string('education')->nullable();
            $table->integer('experience_years')->default(0);
            $table->string('emergency_contact')->nullable();
            $table->string('status')->default('Active'); // Active, Inactive, Leave
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('teacher_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained('teachers')->cascadeOnDelete();
            $table->string('title');
            $table->string('type')->default('Identity'); // Identity, Diploma, Certificate, Contract, Resume
            $table->string('file_path');
            $table->timestamps();
        });

        Schema::create('teacher_certificates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained('teachers')->cascadeOnDelete();
            $table->string('name');
            $table->string('issuing_organization')->nullable();
            $table->date('issue_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->timestamps();
        });

        Schema::create('teacher_experiences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained('teachers')->cascadeOnDelete();
            $table->string('company');
            $table->string('role');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('teacher_availability', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained('teachers')->cascadeOnDelete();
            $table->integer('day_of_week'); // 0 (Sunday) to 6 (Saturday)
            $table->time('start_time');
            $table->time('end_time');
            $table->boolean('is_recurring')->default(true);
            $table->timestamps();
        });

        Schema::create('teacher_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained('teachers')->cascadeOnDelete();
            $table->foreignId('classroom_id')->nullable()->constrained('classrooms')->nullOnDelete();
            $table->foreignId('course_id')->nullable()->constrained('courses')->nullOnDelete();
            $table->date('date');
            $table->time('start_time');
            $table->time('end_time');
            $table->timestamps();
        });

        Schema::create('teacher_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained('teachers')->cascadeOnDelete();
            $table->foreignId('author_id')->constrained('users')->cascadeOnDelete();
            $table->text('note');
            $table->timestamps();
        });

        Schema::create('teacher_activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained('teachers')->cascadeOnDelete();
            $table->string('action');
            $table->text('details')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });

        Schema::create('teacher_performance', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained('teachers')->cascadeOnDelete();
            $table->decimal('attendance_rate', 5, 2)->default(100.00);
            $table->decimal('student_satisfaction', 3, 2)->default(5.00);
            $table->integer('lesson_count')->default(0);
            $table->decimal('feedback_score', 3, 2)->default(5.00);
            $table->string('kpi_month')->nullable();
            $table->timestamps();
        });

        Schema::create('teacher_leave_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained('teachers')->cascadeOnDelete();
            $table->date('start_date');
            $table->date('end_date');
            $table->string('reason')->nullable();
            $table->string('status')->default('Pending'); // Pending, Approved, Rejected
            $table->timestamps();
        });

        Schema::create('teacher_salary_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained('teachers')->cascadeOnDelete();
            $table->decimal('base_salary', 12, 2)->default(0.00);
            $table->string('payment_type')->default('Monthly'); // Monthly, Hourly
            $table->decimal('bonus', 12, 2)->default(0.00);
            $table->decimal('deductions', 12, 2)->default(0.00);
            $table->timestamps();
        });

        Schema::create('teacher_contracts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained('teachers')->cascadeOnDelete();
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->string('employment_type')->default('Full-time'); // Full-time, Part-time
            $table->string('status')->default('Active'); // Active, Expired
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teacher_contracts');
        Schema::dropIfExists('teacher_salary_profiles');
        Schema::dropIfExists('teacher_leave_requests');
        Schema::dropIfExists('teacher_performance');
        Schema::dropIfExists('teacher_activity_logs');
        Schema::dropIfExists('teacher_notes');
        Schema::dropIfExists('teacher_schedules');
        Schema::dropIfExists('teacher_availability');
        Schema::dropIfExists('teacher_experiences');
        Schema::dropIfExists('teacher_certificates');
        Schema::dropIfExists('teacher_documents');
        Schema::dropIfExists('teachers');
    }
};
