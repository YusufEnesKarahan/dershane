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

        Schema::dropIfExists('student_notes');
        Schema::dropIfExists('student_status_histories');
        Schema::dropIfExists('student_transfers');
        Schema::dropIfExists('student_enrollments');
        Schema::dropIfExists('student_documents');
        Schema::dropIfExists('student_addresses');
        Schema::dropIfExists('student_contacts');
        Schema::dropIfExists('student_guardians');
        Schema::dropIfExists('students');

        Schema::enableForeignKeyConstraints();

        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('student_number')->unique();
            $table->string('identity_number')->nullable()->index();
            $table->string('first_name');
            $table->string('last_name');
            $table->date('birth_date')->nullable();
            $table->string('gender')->nullable();
            $table->string('photo')->nullable();
            $table->foreignId('branch_id')->constrained('branches')->cascadeOnDelete();
            $table->foreignId('classroom_id')->nullable()->constrained('classrooms')->nullOnDelete();
            $table->string('status')->default('Active'); // Active, Inactive, Graduated, Left
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('student_guardians', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->string('guardian_name');
            $table->string('relation'); // Anne, Baba, Vasi
            $table->string('phone');
            $table->string('email')->nullable();
            $table->boolean('is_primary')->default(true);
            $table->timestamps();
        });

        Schema::create('student_contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('emergency_contact')->nullable();
            $table->string('emergency_phone')->nullable();
            $table->timestamps();
        });

        Schema::create('student_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->string('city')->nullable();
            $table->string('district')->nullable();
            $table->text('address_text')->nullable();
            $table->timestamps();
        });

        Schema::create('student_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->string('title');
            $table->string('type')->default('Diploma'); // Diploma, Identity, Agreement
            $table->string('file_path');
            $table->timestamps();
        });

        Schema::create('student_enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->foreignId('academic_term_id')->nullable()->constrained('academic_terms')->nullOnDelete();
            $table->string('status')->default('Active'); // Active, Completed, Dropped
            $table->decimal('price_paid', 10, 2)->default(0);
            $table->date('enrollment_date');
            $table->timestamps();

            $table->unique(['student_id', 'course_id']);
        });

        Schema::create('student_transfers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('from_branch_id')->constrained('branches')->cascadeOnDelete();
            $table->foreignId('to_branch_id')->constrained('branches')->cascadeOnDelete();
            $table->foreignId('from_classroom_id')->nullable()->constrained('classrooms')->nullOnDelete();
            $table->foreignId('to_classroom_id')->nullable()->constrained('classrooms')->nullOnDelete();
            $table->text('reason')->nullable();
            $table->date('transfer_date');
            $table->timestamps();
        });

        Schema::create('student_status_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->string('old_status')->nullable();
            $table->string('new_status');
            $table->text('reason')->nullable();
            $table->foreignId('changed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('student_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('author_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('note');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();

        Schema::dropIfExists('student_notes');
        Schema::dropIfExists('student_status_histories');
        Schema::dropIfExists('student_transfers');
        Schema::dropIfExists('student_enrollments');
        Schema::dropIfExists('student_documents');
        Schema::dropIfExists('student_addresses');
        Schema::dropIfExists('student_contacts');
        Schema::dropIfExists('student_guardians');
        Schema::dropIfExists('students');

        Schema::enableForeignKeyConstraints();
    }
};
