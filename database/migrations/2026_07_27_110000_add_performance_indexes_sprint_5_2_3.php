<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->index(['due_date', 'status']);
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->index(['payment_date', 'status']);
        });

        Schema::table('student_debts', function (Blueprint $table) {
            $table->index(['due_date', 'status']);
        });

        Schema::table('lead_followups', function (Blueprint $table) {
            $table->index(['followup_date', 'status']);
        });

        Schema::table('student_admissions', function (Blueprint $table) {
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropIndex(['due_date', 'status']);
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex(['payment_date', 'status']);
        });

        Schema::table('student_debts', function (Blueprint $table) {
            $table->dropIndex(['due_date', 'status']);
        });

        Schema::table('lead_followups', function (Blueprint $table) {
            $table->dropIndex(['followup_date', 'status']);
        });

        Schema::table('student_admissions', function (Blueprint $table) {
            $table->dropIndex(['status']);
        });
    }
};
