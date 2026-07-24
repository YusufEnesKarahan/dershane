<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('student_enrollments');
        Schema::enableForeignKeyConstraints();

        // 1. student_admissions
        Schema::create('student_admissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->nullable()->constrained('crm_leads')->nullOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->foreignId('advisor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('admission_no')->unique(); // e.g. ADM-2026-0001
            $table->string('first_name');
            $table->string('last_name');
            $table->string('tc_no')->nullable();
            $table->string('phone');
            $table->string('email')->nullable();
            $table->string('guardian_name')->nullable();
            $table->string('guardian_phone')->nullable();
            $table->string('guardian_tc_no')->nullable();
            $table->string('school')->nullable();
            $table->string('grade')->nullable();
            $table->string('program')->nullable();
            $table->decimal('total_amount', 12, 2)->default(0.00);
            $table->decimal('deposit_amount', 12, 2)->default(0.00);
            $table->string('status')->default('pre_registration'); 
            // Workflow states: lead_converted, pre_registration, student_info_completed, document_pending, document_completed, contract_ready, payment_pending, enrolled, active_student
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // 2. admission_documents
        Schema::create('admission_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_admission_id')->constrained('student_admissions')->cascadeOnDelete();
            $table->string('document_type'); // Kimlik, Fotoğraf, Veli Belgesi, Sözleşme, Diploma
            $table->string('file_name');
            $table->string('file_path');
            $table->string('status')->default('pending'); // pending, approved, rejected
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
        });

        // 3. admission_notes
        Schema::create('admission_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_admission_id')->constrained('student_admissions')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('note_text');
            $table->timestamps();
        });

        // 4. admission_status_logs
        Schema::create('admission_status_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_admission_id')->constrained('student_admissions')->cascadeOnDelete();
            $table->string('from_status')->nullable();
            $table->string('to_status');
            $table->text('description')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // 5. contract_templates
        Schema::create('contract_templates', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('code')->unique();
            $table->longText('content'); // Contains dynamic placeholders like {student_name}, {tc_no}, {program}, {total_amount}
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 6. enrollment_contracts
        Schema::create('enrollment_contracts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_admission_id')->constrained('student_admissions')->cascadeOnDelete();
            $table->foreignId('contract_template_id')->nullable()->constrained('contract_templates')->nullOnDelete();
            $table->string('contract_no')->unique();
            $table->longText('rendered_content');
            $table->string('status')->default('draft'); // draft, signed, archived
            $table->timestamp('signed_at')->nullable();
            $table->timestamps();
        });

        // 7. student_enrollments
        Schema::create('student_enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_admission_id')->constrained('student_admissions')->cascadeOnDelete();
            $table->foreignId('student_id')->nullable()->constrained('students')->nullOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->foreignId('classroom_id')->nullable()->constrained('classrooms')->nullOnDelete();
            $table->foreignId('invoice_id')->nullable()->constrained('invoices')->nullOnDelete();
            $table->string('enrollment_no')->unique();
            $table->date('enrollment_date');
            $table->string('academic_year')->default('2026-2027');
            $table->decimal('final_fee', 12, 2)->default(0.00);
            $table->string('status')->default('completed'); // completed, cancelled
            $table->timestamps();
        });

        // 8. registration_payments
        Schema::create('registration_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_admission_id')->constrained('student_admissions')->cascadeOnDelete();
            $table->decimal('amount', 12, 2);
            $table->string('payment_method'); // Cash, Credit Card, Bank Transfer
            $table->string('reference_no')->nullable();
            $table->date('payment_date');
            $table->foreignId('received_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('registration_payments');
        Schema::dropIfExists('student_enrollments');
        Schema::dropIfExists('enrollment_contracts');
        Schema::dropIfExists('contract_templates');
        Schema::dropIfExists('admission_status_logs');
        Schema::dropIfExists('admission_notes');
        Schema::dropIfExists('admission_documents');
        Schema::dropIfExists('student_admissions');
    }
};
