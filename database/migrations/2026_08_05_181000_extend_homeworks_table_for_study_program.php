<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('homeworks')) {
            Schema::table('homeworks', function (Blueprint $table) {
                if (!Schema::hasColumn('homeworks', 'week_number')) {
                    $table->integer('week_number')->nullable()->after('classroom_id');
                }
                if (!Schema::hasColumn('homeworks', 'start_date')) {
                    $table->date('start_date')->nullable()->after('week_number');
                }
                if (!Schema::hasColumn('homeworks', 'subject')) {
                    $table->string('subject')->nullable()->after('title');
                }
                if (!Schema::hasColumn('homeworks', 'source_book')) {
                    $table->string('source_book')->nullable()->after('subject');
                }
                if (!Schema::hasColumn('homeworks', 'page_range')) {
                    $table->string('page_range')->nullable()->after('source_book');
                }
                if (!Schema::hasColumn('homeworks', 'video_url')) {
                    $table->string('video_url')->nullable()->after('page_range');
                }
                if (!Schema::hasColumn('homeworks', 'attachment_path')) {
                    $table->string('attachment_path')->nullable()->after('video_url');
                }
                if (!Schema::hasColumn('homeworks', 'priority')) {
                    $table->string('priority')->default('medium')->after('attachment_path'); // low, medium, high, urgent
                }
                if (!Schema::hasColumn('homeworks', 'estimated_minutes')) {
                    $table->integer('estimated_minutes')->default(45)->after('priority');
                }
                if (!Schema::hasColumn('homeworks', 'status')) {
                    $table->string('status')->default('published')->after('estimated_minutes'); // draft, published, completed
                }
            });
        }

        if (Schema::hasTable('homework_submissions')) {
            Schema::table('homework_submissions', function (Blueprint $table) {
                if (!Schema::hasColumn('homework_submissions', 'task_status')) {
                    $table->string('task_status')->default('Not Started')->after('status'); // Not Started, In Progress, Completed
                }
                if (!Schema::hasColumn('homework_submissions', 'progress_percentage')) {
                    $table->integer('progress_percentage')->default(0)->after('task_status');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('homeworks')) {
            Schema::table('homeworks', function (Blueprint $table) {
                $columns = ['week_number', 'start_date', 'subject', 'source_book', 'page_range', 'video_url', 'attachment_path', 'priority', 'estimated_minutes', 'status'];
                foreach ($columns as $col) {
                    if (Schema::hasColumn('homeworks', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }

        if (Schema::hasTable('homework_submissions')) {
            Schema::table('homework_submissions', function (Blueprint $table) {
                if (Schema::hasColumn('homework_submissions', 'task_status')) {
                    $table->dropColumn('task_status');
                }
                if (Schema::hasColumn('homework_submissions', 'progress_percentage')) {
                    $table->dropColumn('progress_percentage');
                }
            });
        }
    }
};
