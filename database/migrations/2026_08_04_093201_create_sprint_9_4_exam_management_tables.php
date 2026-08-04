<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Drop existing exam tables if they exist to apply new schema
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('exam_rankings');
        Schema::dropIfExists('exam_answers');
        Schema::dropIfExists('exam_results');
        Schema::dropIfExists('exam_subjects');
        Schema::dropIfExists('exams');
        Schema::enableForeignKeyConstraints();

        Schema::create('exams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches')->cascadeOnDelete();
            $table->foreignId('academic_term_id')->nullable()->constrained('academic_terms')->nullOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('type')->default('mock_exam'); // mock_exam, practice_exam, final_exam, quiz
            $table->date('exam_date');
            $table->integer('duration_minutes')->nullable();
            $table->decimal('total_score', 8, 2)->default(100.00);
            $table->string('status')->default('draft'); // draft, published, completed, cancelled
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('exam_subjects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches')->cascadeOnDelete();
            $table->foreignId('exam_id')->constrained('exams')->cascadeOnDelete();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->integer('question_count')->default(0);
            $table->decimal('max_score', 8, 2)->default(100.00);
            $table->timestamps();
        });

        Schema::create('exam_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches')->cascadeOnDelete();
            $table->foreignId('exam_id')->constrained('exams')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->decimal('score', 8, 2)->default(0.00);
            $table->integer('rank')->nullable();
            $table->decimal('percentile', 5, 2)->nullable();
            $table->integer('correct_answers')->default(0);
            $table->integer('wrong_answers')->default(0);
            $table->integer('empty_answers')->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->unique(['exam_id', 'student_id']);
        });

        Schema::create('exam_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches')->cascadeOnDelete();
            $table->foreignId('exam_result_id')->constrained('exam_results')->cascadeOnDelete();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->integer('correct')->default(0);
            $table->integer('wrong')->default(0);
            $table->integer('empty')->default(0);
            $table->timestamps();
            
            $table->unique(['exam_result_id', 'course_id']);
        });

        Schema::create('exam_rankings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches')->cascadeOnDelete();
            $table->foreignId('exam_id')->constrained('exams')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->decimal('score', 8, 2)->default(0.00);
            $table->integer('rank')->default(0);
            $table->timestamps();
            
            $table->unique(['exam_id', 'student_id']);
        });
    }

    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('exam_rankings');
        Schema::dropIfExists('exam_answers');
        Schema::dropIfExists('exam_results');
        Schema::dropIfExists('exam_subjects');
        Schema::dropIfExists('exams');
        Schema::enableForeignKeyConstraints();
    }
};
