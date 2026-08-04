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
        if (Schema::hasTable('exam_results')) {
            Schema::table('exam_results', function (Blueprint $table) {
                if (!Schema::hasColumn('exam_results', 'total_net')) {
                    $table->decimal('total_net', 8, 2)->default(0.00)->nullable()->after('score');
                }
                if (!Schema::hasColumn('exam_results', 'is_absent')) {
                    $table->boolean('is_absent')->default(false)->after('empty_answers');
                }
            });
        }

        if (Schema::hasTable('subscriptions') && !Schema::hasColumn('subscriptions', 'branch_id')) {
            Schema::table('subscriptions', function (Blueprint $table) {
                $table->foreignId('branch_id')->nullable()->after('license_id')->constrained('branches')->cascadeOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('exam_results')) {
            Schema::table('exam_results', function (Blueprint $table) {
                if (Schema::hasColumn('exam_results', 'total_net')) {
                    $table->dropColumn('total_net');
                }
                if (Schema::hasColumn('exam_results', 'is_absent')) {
                    $table->dropColumn('is_absent');
                }
            });
        }

        if (Schema::hasTable('subscriptions') && Schema::hasColumn('subscriptions', 'branch_id')) {
            Schema::table('subscriptions', function (Blueprint $table) {
                $table->dropForeign(['branch_id']);
                $table->dropColumn('branch_id');
            });
        }
    }
};
