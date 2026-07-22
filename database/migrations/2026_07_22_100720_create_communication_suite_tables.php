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

        Schema::dropIfExists('announcement_reads');
        Schema::dropIfExists('announcements');
        Schema::dropIfExists('announcement_groups');
        Schema::dropIfExists('notification_logs');
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('notification_templates');
        Schema::dropIfExists('mail_templates');
        Schema::dropIfExists('sms_providers');

        Schema::enableForeignKeyConstraints();

        Schema::create('sms_providers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique(); // netgsm, mutlucell, ias
            $table->string('api_key')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('mail_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('subject');
            $table->text('body_html');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('notification_templates', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // welcome, invoice_due, exam_result
            $table->string('title');
            $table->text('body');
            $table->string('channel')->default('System'); // SMS, Email, System
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->text('content');
            $table->string('type')->default('System'); // SMS, Email, System
            $table->string('status')->default('Unread'); // Unread, Read
            $table->timestamps();
        });

        Schema::create('notification_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('notification_id')->nullable()->constrained('notifications')->nullOnDelete();
            $table->string('recipient');
            $table->string('channel');
            $table->string('provider')->nullable();
            $table->string('status')->default('Sent'); // Sent, Failed
            $table->text('error_message')->nullable();
            $table->timestamps();
        });

        Schema::create('announcement_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique(); // all_students, all_teachers, branch_hq
            $table->timestamps();
        });

        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->nullable();
            $table->text('content');
            $table->foreignId('announcement_group_id')->nullable()->constrained('announcement_groups')->nullOnDelete();
            $table->boolean('is_published')->default(true);
            $table->boolean('is_active')->default(true);
            $table->dateTime('published_at')->nullable();
            $table->timestamps();
        });

        Schema::create('announcement_reads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('announcement_id')->constrained('announcements')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->dateTime('read_at');
            $table->timestamps();

            $table->unique(['announcement_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();

        Schema::dropIfExists('announcement_reads');
        Schema::dropIfExists('announcements');
        Schema::dropIfExists('announcement_groups');
        Schema::dropIfExists('notification_logs');
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('notification_templates');
        Schema::dropIfExists('mail_templates');
        Schema::dropIfExists('sms_providers');

        Schema::enableForeignKeyConstraints();
    }
};
