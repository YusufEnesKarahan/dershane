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
        Schema::create('hq_logs', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('tenant_id')->nullable()->constrained('hq_tenants')->nullOnDelete();
            $table->string('service')->index();
            $table->string('level')->index(); // debug, info, warning, error, critical
            $table->text('message');
            $table->json('context')->nullable();
            $table->string('trace_id')->nullable()->index();
            $table->timestamp('created_at')->useCurrent();
        });

        Schema::create('hq_metrics', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('tenant_id')->nullable()->constrained('hq_tenants')->nullOnDelete();
            $table->string('metric_name')->index();
            $table->string('metric_type')->index(); // counter, gauge, timing
            $table->decimal('value', 20, 4);
            $table->string('unit')->nullable();
            $table->json('tags')->nullable();
            $table->timestamp('recorded_at')->useCurrent()->index();
        });

        Schema::create('hq_metric_snapshots', function (Blueprint $table) {
            $table->id();
            $table->string('metric_name')->index();
            $table->string('aggregation_type'); // avg, sum, max, min
            $table->decimal('value', 20, 4);
            $table->string('period'); // hourly, daily, monthly
            $table->timestamp('snapshot_date')->index();
            
            $table->unique(['metric_name', 'aggregation_type', 'period', 'snapshot_date'], 'hq_metric_snap_unique');
        });

        Schema::create('hq_traces', function (Blueprint $table) {
            $table->id();
            $table->string('trace_id')->index();
            $table->foreignId('tenant_id')->nullable()->constrained('hq_tenants')->nullOnDelete();
            $table->string('service_name')->index();
            $table->string('operation');
            $table->integer('duration_ms');
            $table->string('status')->default('success'); // success, error
            $table->json('metadata')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });

        Schema::create('hq_security_events', function (Blueprint $table) {
            $table->id();
            $table->string('event_type')->index();
            $table->string('severity')->index(); // low, medium, high, critical
            $table->foreignId('tenant_id')->nullable()->constrained('hq_tenants')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('ip')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
        });

        Schema::create('hq_health_checks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained('hq_tenants')->nullOnDelete();
            $table->string('component')->index();
            $table->string('status'); // healthy, warning, critical
            $table->integer('response_time')->nullable(); // ms
            $table->json('details')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hq_health_checks');
        Schema::dropIfExists('hq_security_events');
        Schema::dropIfExists('hq_traces');
        Schema::dropIfExists('hq_metric_snapshots');
        Schema::dropIfExists('hq_metrics');
        Schema::dropIfExists('hq_logs');
    }
};
