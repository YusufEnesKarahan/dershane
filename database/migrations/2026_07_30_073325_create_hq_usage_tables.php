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
        // Raw Usage Metrics
        Schema::create('hq_usage_metrics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('hq_tenants')->cascadeOnDelete();
            $table->string('metric_key')->index(); // e.g. 'students', 'storage_bytes'
            $table->decimal('metric_value', 20, 2);
            $table->timestamp('reported_at');
            $table->timestamps();
            
            $table->index(['tenant_id', 'metric_key', 'reported_at']);
        });

        // Aggregated Usage Snapshots
        Schema::create('hq_usage_snapshots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('hq_tenants')->cascadeOnDelete();
            $table->enum('period', ['hourly', 'daily', 'weekly', 'monthly']);
            $table->timestamp('period_start');
            $table->timestamp('period_end');
            $table->json('data_json'); // Stores all aggregated metrics for that period
            $table->timestamps();

            $table->index(['tenant_id', 'period', 'period_start']);
        });

        // Custom Quota Rules (Overrides global subscription limits for specific tenants)
        Schema::create('hq_quota_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained('hq_tenants')->cascadeOnDelete();
            $table->string('metric_key')->index();
            $table->decimal('warning_threshold', 20, 2)->nullable();
            $table->decimal('critical_threshold', 20, 2);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            // If tenant_id is null, it's a global default override (if needed).
            // Usually, subscription limits define quotas. This is for explicit overrides.
        });

        // Quota Violations Log
        Schema::create('hq_quota_violations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('hq_tenants')->cascadeOnDelete();
            $table->string('metric_key')->index();
            $table->decimal('limit_value', 20, 2);
            $table->decimal('actual_value', 20, 2);
            $table->enum('severity', ['warning', 'critical']);
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hq_quota_violations');
        Schema::dropIfExists('hq_quota_rules');
        Schema::dropIfExists('hq_usage_snapshots');
        Schema::dropIfExists('hq_usage_metrics');
    }
};
