<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('announcements')) {
            Schema::table('announcements', function (Blueprint $table) {
                if (!Schema::hasColumn('announcements', 'slug')) {
                    $table->string('slug')->nullable()->after('title');
                }
                if (!Schema::hasColumn('announcements', 'summary')) {
                    $table->text('summary')->nullable()->after('slug');
                }
                if (!Schema::hasColumn('announcements', 'cover_image')) {
                    $table->string('cover_image')->nullable()->after('content');
                }
                if (!Schema::hasColumn('announcements', 'category_id')) {
                    $table->foreignId('category_id')->nullable()->after('cover_image')->constrained('announcement_categories')->nullOnDelete();
                }
                if (!Schema::hasColumn('announcements', 'publish_at')) {
                    $table->timestamp('publish_at')->nullable()->after('published_at');
                }
                if (!Schema::hasColumn('announcements', 'expire_at')) {
                    $table->timestamp('expire_at')->nullable()->after('publish_at');
                }
                if (!Schema::hasColumn('announcements', 'is_pinned')) {
                    $table->boolean('is_pinned')->default(false)->after('expire_at');
                }
                if (!Schema::hasColumn('announcements', 'is_popup')) {
                    $table->boolean('is_popup')->default(false)->after('is_pinned');
                }
                if (!Schema::hasColumn('announcements', 'is_all_branches')) {
                    $table->boolean('is_all_branches')->default(true)->after('is_popup');
                }
                if (!Schema::hasColumn('announcements', 'notify_roles')) {
                    $table->json('notify_roles')->nullable()->after('is_all_branches');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('announcements')) {
            Schema::table('announcements', function (Blueprint $table) {
                $cols = ['slug', 'summary', 'cover_image', 'category_id', 'publish_at', 'expire_at', 'is_pinned', 'is_popup', 'is_all_branches', 'notify_roles'];
                foreach ($cols as $c) {
                    if (Schema::hasColumn('announcements', $c)) {
                        $table->dropColumn($c);
                    }
                }
            });
        }
    }
};
