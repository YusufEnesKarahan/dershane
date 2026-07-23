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

        Schema::dropIfExists('dashboard_snapshots');
        Schema::dropIfExists('analytics_cache');
        Schema::dropIfExists('report_exports');
        Schema::dropIfExists('report_schedules');
        Schema::dropIfExists('executive_reports');

        Schema::enableForeignKeyConstraints();

        Schema::create('dashboard_snapshots', function (Blueprint $table) {
            $table->id();
            $table->json('metrics');
            $table->timestamps();
        });

        Schema::create('analytics_cache', function (Blueprint $table) {
            $table->id();
            $table->string('cache_key')->unique();
            $table->text('cache_value');
            $table->timestamp('expires_at');
            $table->timestamps();
        });

        Schema::create('report_exports', function (Blueprint $table) {
            $table->id();
            $table->string('report_type'); // Executive, Financial, Academic
            $table->string('format'); // PDF, Excel, CSV
            $table->string('file_path')->nullable();
            $table->string('status')->default('Pending'); // Pending, Completed, Failed
            $table->foreignId('requested_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('report_schedules', function (Blueprint $table) {
            $table->id();
            $table->string('report_type');
            $table->string('format');
            $table->string('email_recipients');
            $table->string('cron_expression');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('executive_reports', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->json('content_data');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dashboard_snapshots');
        Schema::dropIfExists('analytics_cache');
        Schema::dropIfExists('report_exports');
        Schema::dropIfExists('report_schedules');
        Schema::dropIfExists('executive_reports');
    }
};
