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
        Schema::create('hq_policies', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name')->unique();
            $table->text('description')->nullable();
            $table->string('type'); // compliance, security, operational
            $table->string('severity')->default('medium'); // low, medium, high, critical
            $table->boolean('is_active')->default(true);
            $table->json('logic')->nullable(); // The JSON logic for evaluation (optional if using rules table)
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('hq_policy_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('policy_id')->constrained('hq_policies')->cascadeOnDelete();
            $table->string('metric'); // e.g., backup.success
            $table->string('operator'); // e.g., ==, >, <
            $table->string('value'); 
            $table->string('logical_operator')->default('AND'); // AND, OR between rules
            $table->timestamps();
        });

        Schema::create('hq_policy_assignments', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('policy_id')->constrained('hq_policies')->cascadeOnDelete();
            $table->foreignId('tenant_id')->nullable()->constrained('hq_tenants')->cascadeOnDelete();
            // nullable tenant_id means global assignment
            $table->json('overrides')->nullable();
            $table->timestamps();
            
            $table->unique(['policy_id', 'tenant_id']);
        });

        Schema::create('hq_compliance_frameworks', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name')->unique(); // ISO27001, SOC2, GDPR, etc.
            $table->text('description')->nullable();
            $table->string('version')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('hq_compliance_controls', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('framework_id')->constrained('hq_compliance_frameworks')->cascadeOnDelete();
            $table->string('control_code')->index(); // e.g., A.9.2.1
            $table->string('title');
            $table->text('description')->nullable();
            $table->foreignId('policy_id')->nullable()->constrained('hq_policies')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('hq_compliance_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('hq_tenants')->cascadeOnDelete();
            $table->foreignId('framework_id')->constrained('hq_compliance_frameworks')->cascadeOnDelete();
            $table->decimal('score_percentage', 5, 2)->default(0);
            $table->json('details')->nullable();
            $table->timestamp('evaluated_at')->useCurrent();
            $table->timestamps();
        });

        Schema::create('hq_risk_scores', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('tenant_id')->constrained('hq_tenants')->cascadeOnDelete();
            $table->integer('score')->default(0); // 0-100+
            $table->string('level')->default('healthy'); // healthy, warning, critical
            $table->json('factors')->nullable();
            $table->timestamp('calculated_at')->useCurrent();
            $table->timestamps();
        });

        Schema::create('hq_sla_policies', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name');
            $table->string('metric'); // uptime, response_time, backup_frequency
            $table->string('operator'); // <, >, ==
            $table->string('threshold_value');
            $table->integer('evaluation_period_minutes')->default(60);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('hq_sla_violations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sla_policy_id')->constrained('hq_sla_policies')->cascadeOnDelete();
            $table->foreignId('tenant_id')->constrained('hq_tenants')->cascadeOnDelete();
            $table->string('actual_value');
            $table->string('status')->default('open'); // open, acknowledged, resolved
            $table->timestamp('detected_at')->useCurrent();
            $table->timestamp('resolved_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        Schema::create('hq_governance_audits', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('action'); // policy_created, compliance_passed, sla_violated
            $table->string('entity_type')->nullable();
            $table->unsignedBigInteger('entity_id')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('tenant_id')->nullable()->constrained('hq_tenants')->nullOnDelete();
            $table->json('changes')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hq_governance_audits');
        Schema::dropIfExists('hq_sla_violations');
        Schema::dropIfExists('hq_sla_policies');
        Schema::dropIfExists('hq_risk_scores');
        Schema::dropIfExists('hq_compliance_results');
        Schema::dropIfExists('hq_compliance_controls');
        Schema::dropIfExists('hq_compliance_frameworks');
        Schema::dropIfExists('hq_policy_assignments');
        Schema::dropIfExists('hq_policy_rules');
        Schema::dropIfExists('hq_policies');
    }
};
