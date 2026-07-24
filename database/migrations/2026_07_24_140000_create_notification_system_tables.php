<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('notifications', function (Blueprint $table): void {
            $table->text('message')->nullable()->after('title');
            $table->json('data')->nullable()->after('message');
            $table->string('channel')->default('panel')->after('data');
            $table->string('priority')->default('normal')->after('channel');
            $table->timestamp('read_at')->nullable()->after('priority');
            $table->timestamp('sent_at')->nullable()->after('read_at');
            $table->index(['user_id', 'read_at']);
            $table->index(['channel', 'created_at']);
        });

        Schema::table('notification_templates', function (Blueprint $table): void {
            $table->string('name')->nullable()->after('id');
            $table->string('slug')->nullable()->after('name');
            $table->string('title_template')->nullable()->after('title');
            $table->text('body_template')->nullable()->after('body');
            $table->unique('slug');
        });

        Schema::table('notification_logs', function (Blueprint $table): void {
            $table->timestamp('sent_at')->nullable()->after('error_message');
            $table->index(['channel', 'status']);
        });

        Schema::create('notification_preferences', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->boolean('email_enabled')->default(true);
            $table->boolean('panel_enabled')->default(true);
            $table->boolean('sms_enabled')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_preferences');
        Schema::table('notification_logs', fn (Blueprint $table) => $table->dropIndex(['channel', 'status']));
        Schema::table('notification_templates', fn (Blueprint $table) => $table->dropUnique(['slug']));
        Schema::table('notifications', function (Blueprint $table): void {
            $table->dropIndex(['user_id', 'read_at']);
            $table->dropIndex(['channel', 'created_at']);
        });
    }
};
