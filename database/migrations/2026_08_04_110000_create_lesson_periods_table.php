<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lesson_periods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches')->cascadeOnDelete();
            $table->string('name');
            $table->time('start_time');
            $table->time('end_time');
            $table->timestamps();
        });

        if (Schema::hasTable('lesson_schedules') && !Schema::hasColumn('lesson_schedules', 'lesson_period_id')) {
            Schema::table('lesson_schedules', function (Blueprint $table) {
                $table->foreignId('lesson_period_id')->nullable()->after('course_id')->constrained('lesson_periods')->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('lesson_schedules') && Schema::hasColumn('lesson_schedules', 'lesson_period_id')) {
            Schema::table('lesson_schedules', function (Blueprint $table) {
                $table->dropForeign(['lesson_period_id']);
                $table->dropColumn('lesson_period_id');
            });
        }
        Schema::dropIfExists('lesson_periods');
    }
};
