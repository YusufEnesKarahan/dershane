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

        Schema::dropIfExists('lead_tag_items');
        Schema::dropIfExists('lead_tags');
        Schema::dropIfExists('lead_notes');
        Schema::dropIfExists('lead_activities');
        Schema::dropIfExists('lead_assignments');
        Schema::dropIfExists('lead_followups');
        Schema::dropIfExists('lead_documents');
        Schema::dropIfExists('crm_leads');
        Schema::dropIfExists('lead_statuses');
        Schema::dropIfExists('lead_sources');

        Schema::enableForeignKeyConstraints();

        Schema::create('lead_sources', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('lead_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->string('color')->default('#4F46E5');
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('crm_leads', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('phone');
            $table->string('whatsapp')->nullable();
            $table->string('email')->nullable();
            $table->string('school')->nullable();
            $table->string('grade')->nullable();
            $table->string('city')->nullable();
            $table->string('district')->nullable();
            $table->text('address')->nullable();
            $table->string('guardian_info')->nullable();
            $table->string('program')->nullable(); // TYT, AYT, LGS, YKS, etc.
            $table->string('reference')->nullable();
            $table->foreignId('lead_source_id')->nullable()->constrained('lead_sources')->nullOnDelete();
            $table->foreignId('lead_status_id')->nullable()->constrained('lead_statuses')->nullOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->foreignId('advisor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('lead_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained('crm_leads')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('note_text');
            $table->string('file_path')->nullable();
            $table->timestamps();
        });

        Schema::create('lead_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained('crm_leads')->cascadeOnDelete();
            $table->string('action_type'); // Created, Called, SMS, Email, WhatsApp, Offer, Registered
            $table->text('description');
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('lead_tags', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('color')->default('#EF4444');
            $table->timestamps();
        });

        Schema::create('lead_tag_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained('crm_leads')->cascadeOnDelete();
            $table->foreignId('lead_tag_id')->constrained('lead_tags')->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::create('lead_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained('crm_leads')->cascadeOnDelete();
            $table->foreignId('from_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('to_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('assigned_at')->useCurrent();
        });

        Schema::create('lead_followups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained('crm_leads')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('followup_date');
            $table->text('reminder_note')->nullable();
            $table->string('priority')->default('Medium'); // Low, Medium, High
            $table->string('status')->default('Pending'); // Pending, Completed, Cancelled
            $table->timestamps();
        });

        Schema::create('lead_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained('crm_leads')->cascadeOnDelete();
            $table->string('name');
            $table->string('file_path');
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lead_documents');
        Schema::dropIfExists('lead_followups');
        Schema::dropIfExists('lead_assignments');
        Schema::dropIfExists('lead_tag_items');
        Schema::dropIfExists('lead_tags');
        Schema::dropIfExists('lead_activities');
        Schema::dropIfExists('lead_notes');
        Schema::dropIfExists('crm_leads');
        Schema::dropIfExists('lead_statuses');
        Schema::dropIfExists('lead_sources');
    }
};
