<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('onboarding_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches')->cascadeOnDelete();
            $table->boolean('company_info_completed')->default(false);
            $table->boolean('first_branch_completed')->default(false);
            $table->boolean('teacher_added')->default(false);
            $table->boolean('student_added')->default(false);
            $table->boolean('course_created')->default(false);
            $table->boolean('exam_created')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('onboarding_progress');
    }
};
