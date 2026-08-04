<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('notifications')) {
            Schema::table('notifications', function (Blueprint $table) {
                if (!Schema::hasColumn('notifications', 'branch_id')) {
                    $table->foreignId('branch_id')->nullable()->after('id')->constrained('branches')->cascadeOnDelete();
                }
                if (!Schema::hasColumn('notifications', 'sender_id')) {
                    $table->foreignId('sender_id')->nullable()->after('branch_id')->constrained('users')->nullOnDelete();
                }
                if (!Schema::hasColumn('notifications', 'receiver_id')) {
                    $table->unsignedBigInteger('receiver_id')->nullable()->after('sender_id');
                }
                if (!Schema::hasColumn('notifications', 'receiver_type')) {
                    $table->string('receiver_type')->default('admin')->after('receiver_id');
                }
                if (!Schema::hasColumn('notifications', 'message')) {
                    $table->text('message')->nullable()->after('title');
                }
                if (!Schema::hasColumn('notifications', 'read_at')) {
                    $table->timestamp('read_at')->nullable()->after('message');
                }
            });
        }

        if (Schema::hasTable('notification_templates')) {
            Schema::table('notification_templates', function (Blueprint $table) {
                if (!Schema::hasColumn('notification_templates', 'branch_id')) {
                    $table->foreignId('branch_id')->nullable()->after('id')->constrained('branches')->cascadeOnDelete();
                }
                if (!Schema::hasColumn('notification_templates', 'type')) {
                    $table->string('type')->default('system')->after('name');
                }
                if (!Schema::hasColumn('notification_templates', 'title_template')) {
                    $table->string('title_template')->nullable()->after('type');
                }
                if (!Schema::hasColumn('notification_templates', 'message_template')) {
                    $table->text('message_template')->nullable()->after('title_template');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('notifications')) {
            Schema::table('notifications', function (Blueprint $table) {
                if (Schema::hasColumn('notifications', 'branch_id')) {
                    $table->dropForeign(['branch_id']);
                    $table->dropColumn('branch_id');
                }
                if (Schema::hasColumn('notifications', 'sender_id')) {
                    $table->dropForeign(['sender_id']);
                    $table->dropColumn('sender_id');
                }
                if (Schema::hasColumn('notifications', 'receiver_id')) {
                    $table->dropColumn('receiver_id');
                }
                if (Schema::hasColumn('notifications', 'receiver_type')) {
                    $table->dropColumn('receiver_type');
                }
            });
        }
    }
};
