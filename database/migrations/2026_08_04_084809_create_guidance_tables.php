<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_guidance_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained()->onDelete('cascade');
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('teacher_id')->constrained()->onDelete('cascade'); // The counselor
            $table->foreignId('academic_term_id')->constrained()->onDelete('cascade');
            $table->string('category'); // e.g. Academic, Disciplinary, Psychological
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('priority', ['Low', 'Medium', 'High', 'Critical'])->default('Low');
            $table->enum('status', ['Open', 'In Progress', 'Resolved', 'Closed'])->default('Open');
            $table->date('meeting_date')->nullable();
            $table->date('next_follow_up')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('student_meetings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained()->onDelete('cascade');
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('teacher_id')->constrained()->onDelete('cascade');
            $table->datetime('meeting_date');
            $table->string('meeting_type'); // e.g. Online, In-person
            $table->text('summary')->nullable();
            $table->text('action_plan')->nullable();
            $table->datetime('next_meeting')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('parent_meetings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained()->onDelete('cascade');
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('guardian_id')->constrained('student_guardians')->onDelete('cascade');
            $table->foreignId('teacher_id')->constrained()->onDelete('cascade');
            $table->datetime('meeting_date');
            $table->text('summary')->nullable();
            $table->text('decisions')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('student_goals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained()->onDelete('cascade');
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->string('target_value')->nullable();
            $table->string('current_value')->nullable();
            $table->date('deadline')->nullable();
            $table->enum('status', ['Pending', 'In Progress', 'Achieved', 'Failed'])->default('Pending');
            $table->timestamps();
            $table->softDeletes();
        });



        Schema::create('performance_snapshots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained()->onDelete('cascade');
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('academic_term_id')->constrained()->onDelete('cascade');
            $table->decimal('attendance_rate', 5, 2)->default(100);
            $table->decimal('exam_average', 5, 2)->default(0);
            $table->decimal('homework_completion', 5, 2)->default(0);
            $table->decimal('late_submission_rate', 5, 2)->default(0);
            $table->enum('risk_score', ['Low', 'Medium', 'High', 'Critical'])->default('Low');
            $table->date('snapshot_date');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('student_risk_levels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained()->onDelete('cascade');
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->enum('level', ['Low', 'Medium', 'High', 'Critical'])->default('Low');
            $table->text('reason')->nullable();
            $table->foreignId('updated_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_risk_levels');
        Schema::dropIfExists('performance_snapshots');
        Schema::dropIfExists('student_goals');
        Schema::dropIfExists('parent_meetings');
        Schema::dropIfExists('student_meetings');
        Schema::dropIfExists('student_guidance_records');
    }
};
