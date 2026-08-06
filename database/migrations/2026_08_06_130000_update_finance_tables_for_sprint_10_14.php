<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('students')) {
            Schema::table('students', function (Blueprint $table) {
                if (!Schema::hasColumn('students', 'guardian_id')) {
                    $table->foreignId('guardian_id')->nullable()->after('classroom_id')->constrained('users')->nullOnDelete();
                }
                if (!Schema::hasColumn('students', 'phone')) {
                    $table->string('phone')->nullable()->after('last_name');
                }
                if (!Schema::hasColumn('students', 'tc_no')) {
                    $table->string('tc_no')->nullable()->after('phone');
                }
            });
        }

        if (Schema::hasTable('invoices')) {
            Schema::table('invoices', function (Blueprint $table) {
                if (!Schema::hasColumn('invoices', 'branch_id')) {
                    $table->foreignId('branch_id')->nullable()->after('id')->constrained('branches')->cascadeOnDelete();
                }
                if (!Schema::hasColumn('invoices', 'guardian_id')) {
                    $table->foreignId('guardian_id')->nullable()->after('student_id')->constrained('users')->nullOnDelete();
                }
                if (!Schema::hasColumn('invoices', 'description')) {
                    $table->text('description')->nullable()->after('due_date');
                }
            });
        }

        if (Schema::hasTable('invoice_items')) {
            Schema::table('invoice_items', function (Blueprint $table) {
                if (!Schema::hasColumn('invoice_items', 'item_type')) {
                    $table->string('item_type')->default('Kayıt Ücreti')->after('invoice_id');
                }
            });
        }

        if (Schema::hasTable('payments')) {
            Schema::table('payments', function (Blueprint $table) {
                if (!Schema::hasColumn('payments', 'invoice_id')) {
                    $table->foreignId('invoice_id')->nullable()->after('id')->constrained('invoices')->nullOnDelete();
                }
                if (!Schema::hasColumn('payments', 'payment_number')) {
                    $table->string('payment_number')->nullable()->after('id');
                }
                if (!Schema::hasColumn('payments', 'branch_id')) {
                    $table->foreignId('branch_id')->nullable()->after('id')->constrained('branches')->cascadeOnDelete();
                }
                if (!Schema::hasColumn('payments', 'payment_method')) {
                    $table->string('payment_method')->default('Nakit')->after('amount');
                }
                if (!Schema::hasColumn('payments', 'reference_no')) {
                    $table->string('reference_no')->nullable()->after('payment_method');
                }
                if (!Schema::hasColumn('payments', 'received_by')) {
                    $table->foreignId('received_by')->nullable()->after('reference_no')->constrained('users')->nullOnDelete();
                }
                if (!Schema::hasColumn('payments', 'status')) {
                    $table->string('status')->default('Completed')->after('amount');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('students')) {
            Schema::table('students', function (Blueprint $table) {
                $cols = ['guardian_id', 'phone', 'tc_no'];
                foreach ($cols as $c) {
                    if (Schema::hasColumn('students', $c)) {
                        $table->dropColumn($c);
                    }
                }
            });
        }

        if (Schema::hasTable('invoices')) {
            Schema::table('invoices', function (Blueprint $table) {
                $cols = ['branch_id', 'guardian_id', 'description'];
                foreach ($cols as $c) {
                    if (Schema::hasColumn('invoices', $c)) {
                        $table->dropColumn($c);
                    }
                }
            });
        }

        if (Schema::hasTable('invoice_items')) {
            Schema::table('invoice_items', function (Blueprint $table) {
                if (Schema::hasColumn('invoice_items', 'item_type')) {
                    $table->dropColumn('item_type');
                }
            });
        }

        if (Schema::hasTable('payments')) {
            Schema::table('payments', function (Blueprint $table) {
                $cols = ['invoice_id', 'payment_number', 'branch_id', 'payment_method', 'reference_no', 'received_by', 'status'];
                foreach ($cols as $c) {
                    if (Schema::hasColumn('payments', $c)) {
                        $table->dropColumn($c);
                    }
                }
            });
        }
    }
};
