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

        Schema::dropIfExists('assignment_scores');
        Schema::dropIfExists('assignment_comments');
        Schema::dropIfExists('assignment_files');
        Schema::dropIfExists('assignment_submissions');
        Schema::dropIfExists('assignments');

        Schema::enableForeignKeyConstraints();

        Schema::create('assignments', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->string('assignment_type')->default('Classroom'); // Classroom, Course, Individual
            $table->foreignId('classroom_id')->nullable()->constrained('classrooms')->nullOnDelete();
            $table->foreignId('course_id')->nullable()->constrained('courses')->nullOnDelete();
            $table->foreignId('teacher_id')->constrained('teachers')->cascadeOnDelete();
            $table->dateTime('due_date');
            $table->integer('max_score')->default(100);
            $table->string('status')->default('Published'); // Draft, Published, Archived
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('assignment_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assignment_id')->constrained('assignments')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->dateTime('submission_date');
            $table->boolean('is_late')->default(false);
            $table->text('remarks')->nullable();
            $table->string('status')->default('Pending'); // Pending, Graded, Rejected
            $table->timestamps();

            $table->unique(['assignment_id', 'student_id']);
        });

        Schema::create('assignment_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assignment_id')->nullable()->constrained('assignments')->cascadeOnDelete();
            $table->foreignId('submission_id')->nullable()->constrained('assignment_submissions')->cascadeOnDelete();
            $table->string('title');
            $table->string('file_path');
            $table->string('file_type')->nullable();
            $table->bigInteger('size_bytes')->nullable();
            $table->timestamps();
        });

        Schema::create('assignment_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('submission_id')->constrained('assignment_submissions')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->text('comment');
            $table->timestamps();
        });

        Schema::create('assignment_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('submission_id')->constrained('assignment_submissions')->cascadeOnDelete();
            $table->foreignId('evaluator_id')->nullable()->constrained('users')->nullOnDelete();
            $table->decimal('score', 8, 2)->default(0.00);
            $table->decimal('max_score', 8, 2)->default(100.00);
            $table->text('feedback')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();

        Schema::dropIfExists('assignment_scores');
        Schema::dropIfExists('assignment_comments');
        Schema::dropIfExists('assignment_files');
        Schema::dropIfExists('assignment_submissions');
        Schema::dropIfExists('assignments');

        Schema::enableForeignKeyConstraints();
    }
};
