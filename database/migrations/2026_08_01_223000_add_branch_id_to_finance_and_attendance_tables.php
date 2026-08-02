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
        $tables = [
            'invoices',
            'payments',
            'attendance_sessions',
            'attendances',
            'assignments',
        ];

        foreach ($tables as $table) {
            if (!Schema::hasColumn($table, 'branch_id')) {
                Schema::table($table, function (Blueprint $table_blueprint) {
                    $table_blueprint->foreignId('branch_id')->nullable()->constrained('branches')->cascadeOnDelete();
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = [
            'invoices',
            'payments',
            'attendance_sessions',
            'attendances',
            'assignments',
        ];

        foreach ($tables as $table) {
            if (Schema::hasColumn($table, 'branch_id')) {
                Schema::table($table, function (Blueprint $table_blueprint) use ($table) {
                    $table_blueprint->dropForeign($table . '_branch_id_foreign');
                    $table_blueprint->dropColumn('branch_id');
                });
            }
        }
    }
};
