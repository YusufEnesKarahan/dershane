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

        // Drop any dependencies or old table to avoid constraint errors
        Schema::dropIfExists('course_branches');
        Schema::dropIfExists('course_teachers');
        Schema::dropIfExists('course_prerequisites');
        Schema::dropIfExists('course_pricings');
        Schema::dropIfExists('course_materials');
        Schema::dropIfExists('course_modules');
        Schema::dropIfExists('course_subjects');
        Schema::dropIfExists('courses');
        Schema::dropIfExists('course_levels');

        Schema::enableForeignKeyConstraints();

        Schema::create('course_levels', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->timestamps();
        });

        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->foreignId('course_level_id')->nullable()->constrained('course_levels')->nullOnDelete();
            $table->string('duration')->nullable();
            $table->integer('capacity')->default(0);
            $table->string('status')->default('Draft'); // Draft, Published
            $table->boolean('is_active')->default(true)->index();
            $table->string('cover_image')->nullable();
            $table->string('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            $table->text('seo_keywords')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('course_subjects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('course_modules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('course_materials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->string('title');
            $table->string('type')->default('PDF'); // PDF, Video, Doc
            $table->string('file_path');
            $table->timestamps();
        });

        Schema::create('course_pricings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->decimal('price', 10, 2);
            $table->string('currency')->default('TRY');
            $table->integer('installment_options')->default(1);
            $table->timestamps();
        });

        Schema::create('course_prerequisites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->foreignId('prerequisite_id')->constrained('courses')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['course_id', 'prerequisite_id']);
        });

        Schema::create('course_teachers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->foreignId('teacher_id')->constrained('teachers')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['course_id', 'teacher_id']);
        });

        Schema::create('course_branches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained('branches')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['course_id', 'branch_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_branches');
        Schema::dropIfExists('course_teachers');
        Schema::dropIfExists('course_prerequisites');
        Schema::dropIfExists('course_pricings');
        Schema::dropIfExists('course_materials');
        Schema::dropIfExists('course_modules');
        Schema::dropIfExists('course_subjects');
        Schema::dropIfExists('courses');
        Schema::dropIfExists('course_levels');
    }
};
