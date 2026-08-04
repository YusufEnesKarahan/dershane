<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Homeworks Table
        Schema::create('homeworks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches')->cascadeOnDelete();
            $table->foreignId('academic_term_id')->constrained('academic_terms')->cascadeOnDelete();
            $table->foreignId('classroom_id')->constrained('classrooms')->cascadeOnDelete();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->foreignId('teacher_id')->constrained('teachers')->cascadeOnDelete();
            
            $table->string('title');
            $table->text('description')->nullable();
            
            $table->timestamp('publish_at')->nullable();
            $table->timestamp('due_date');
            
            $table->boolean('allow_late_submission')->default(false);
            $table->integer('max_score')->default(100);
            
            $table->string('status')->default('draft'); // draft, published, closed
            
            $table->timestamps();
            $table->softDeletes();
        });

        // 2. Homework Submissions Table
        Schema::create('homework_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches')->cascadeOnDelete();
            $table->foreignId('homework_id')->constrained('homeworks')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            
            $table->timestamp('submitted_at')->nullable();
            $table->integer('score')->nullable();
            $table->text('feedback')->nullable();
            
            $table->string('status')->default('pending'); // pending, submitted, late, graded
            
            $table->foreignId('graded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('graded_at')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->unique(['homework_id', 'student_id']);
        });

        // 3. Homework Files Table
        Schema::create('homework_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches')->cascadeOnDelete();
            $table->foreignId('homework_id')->nullable()->constrained('homeworks')->cascadeOnDelete();
            $table->foreignId('homework_submission_id')->nullable()->constrained('homework_submissions')->cascadeOnDelete();
            
            $table->string('disk')->default('public');
            $table->string('path');
            $table->string('original_name');
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('size')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('homework_files');
        Schema::dropIfExists('homework_submissions');
        Schema::dropIfExists('homeworks');
    }
};
