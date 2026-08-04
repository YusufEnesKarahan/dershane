<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Update homeworks table
        Schema::table('homeworks', function (Blueprint $table) {
            if (!Schema::hasColumn('homeworks', 'homework_type')) {
                $table->string('homework_type')->default('standard')->after('description');
            }
            if (!Schema::hasColumn('homeworks', 'assigned_date')) {
                $table->date('assigned_date')->nullable()->after('homework_type');
            }
            if (!Schema::hasColumn('homeworks', 'attachment_path')) {
                $table->string('attachment_path')->nullable()->after('status');
            }
        });

        // 2. Update homework_submissions table
        Schema::table('homework_submissions', function (Blueprint $table) {
            // Rename score to grade if score exists and grade does not
            if (Schema::hasColumn('homework_submissions', 'score') && !Schema::hasColumn('homework_submissions', 'grade')) {
                $table->renameColumn('score', 'grade');
            } else if (!Schema::hasColumn('homework_submissions', 'grade')) {
                $table->integer('grade')->nullable()->after('status');
            }

            // Rename feedback to teacher_feedback if feedback exists and teacher_feedback does not
            if (Schema::hasColumn('homework_submissions', 'feedback') && !Schema::hasColumn('homework_submissions', 'teacher_feedback')) {
                $table->renameColumn('feedback', 'teacher_feedback');
            } else if (!Schema::hasColumn('homework_submissions', 'teacher_feedback')) {
                $table->text('teacher_feedback')->nullable()->after('grade');
            }

            if (!Schema::hasColumn('homework_submissions', 'attachment_path')) {
                $table->string('attachment_path')->nullable()->after('teacher_feedback');
            }
        });

        // 3. Create homework_comments table
        if (!Schema::hasTable('homework_comments')) {
            Schema::create('homework_comments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('branch_id')->constrained('branches')->cascadeOnDelete();
                $table->foreignId('homework_id')->constrained('homeworks')->cascadeOnDelete();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                
                $table->text('comment');
                
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('homework_comments');

        Schema::table('homework_submissions', function (Blueprint $table) {
            if (Schema::hasColumn('homework_submissions', 'attachment_path')) {
                $table->dropColumn('attachment_path');
            }
            if (Schema::hasColumn('homework_submissions', 'grade')) {
                $table->renameColumn('grade', 'score');
            }
            if (Schema::hasColumn('homework_submissions', 'teacher_feedback')) {
                $table->renameColumn('teacher_feedback', 'feedback');
            }
        });

        Schema::table('homeworks', function (Blueprint $table) {
            if (Schema::hasColumn('homeworks', 'homework_type')) {
                $table->dropColumn('homework_type');
            }
            if (Schema::hasColumn('homeworks', 'assigned_date')) {
                $table->dropColumn('assigned_date');
            }
            if (Schema::hasColumn('homeworks', 'attachment_path')) {
                $table->dropColumn('attachment_path');
            }
        });
    }
};
