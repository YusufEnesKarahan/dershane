<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('course_teachers')) {
            Schema::table('course_teachers', function (Blueprint $table) {
                if (!Schema::hasColumn('course_teachers', 'is_primary')) {
                    $table->boolean('is_primary')->default(true)->after('teacher_id');
                }
                if (!Schema::hasColumn('course_teachers', 'role')) {
                    $table->string('role')->default('Primary')->after('is_primary'); // Primary, Assistant, Supporter
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('course_teachers')) {
            Schema::table('course_teachers', function (Blueprint $table) {
                if (Schema::hasColumn('course_teachers', 'is_primary')) {
                    $table->dropColumn('is_primary');
                }
                if (Schema::hasColumn('course_teachers', 'role')) {
                    $table->dropColumn('role');
                }
            });
        }
    }
};
