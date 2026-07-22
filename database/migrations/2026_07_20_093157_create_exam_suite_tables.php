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

        Schema::dropIfExists('exam_rankings');
        Schema::dropIfExists('exam_subject_results');
        Schema::dropIfExists('exam_answers');
        Schema::dropIfExists('exam_results');
        Schema::dropIfExists('exam_sessions');
        Schema::dropIfExists('exams');

        Schema::enableForeignKeyConstraints();

        Schema::create('exams', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('code')->unique();
            $table->string('exam_type')->default('Trial'); // Trial, TYT, AYT, Subject
            $table->date('exam_date');
            $table->integer('total_questions')->default(120);
            $table->integer('duration_minutes')->default(135);
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });

        Schema::create('exam_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained('exams')->cascadeOnDelete();
            $table->foreignId('classroom_id')->nullable()->constrained('classrooms')->nullOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->date('session_date');
            $table->time('start_time')->default('09:00:00');
            $table->timestamps();
        });

        Schema::create('exam_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained('exams')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->integer('total_correct')->default(0);
            $table->integer('total_wrong')->default(0);
            $table->integer('total_empty')->default(0);
            $table->decimal('total_net', 8, 2)->default(0.00);
            $table->decimal('score', 8, 2)->default(0.00);
            $table->integer('branch_rank')->nullable();
            $table->integer('global_rank')->nullable();
            $table->boolean('is_absent')->default(false);
            $table->timestamps();

            $table->unique(['exam_id', 'student_id']);
        });

        Schema::create('exam_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_result_id')->constrained('exam_results')->cascadeOnDelete();
            $table->integer('question_number');
            $table->string('student_answer', 1)->nullable();
            $table->string('correct_answer', 1);
            $table->boolean('is_correct')->default(false);
            $table->timestamps();
        });

        Schema::create('exam_subject_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_result_id')->constrained('exam_results')->cascadeOnDelete();
            $table->string('subject_name'); // Türkçe, Matematik, Fen, Sosyal
            $table->integer('correct_count')->default(0);
            $table->integer('wrong_count')->default(0);
            $table->integer('empty_count')->default(0);
            $table->decimal('net_count', 8, 2)->default(0.00);
            $table->timestamps();
        });

        Schema::create('exam_rankings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained('exams')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained('branches')->cascadeOnDelete();
            $table->decimal('score', 8, 2)->default(0.00);
            $table->integer('branch_rank')->default(1);
            $table->integer('global_rank')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();

        Schema::dropIfExists('exam_rankings');
        Schema::dropIfExists('exam_subject_results');
        Schema::dropIfExists('exam_answers');
        Schema::dropIfExists('exam_results');
        Schema::dropIfExists('exam_sessions');
        Schema::dropIfExists('exams');

        Schema::enableForeignKeyConstraints();
    }
};
