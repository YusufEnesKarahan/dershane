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
        Schema::create('hq_workflows', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('trigger_event'); // e.g. 'App\Events\SystemOfflineDetected'
            $table->json('trigger_conditions')->nullable(); // Optional initial conditions to even start the workflow
            $table->boolean('is_active')->default(true);
            $table->json('metadata')->nullable(); // Additional config
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('hq_workflow_steps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hq_workflow_id')->constrained('hq_workflows')->onDelete('cascade');
            $table->string('type'); // 'condition', 'action', 'delay'
            $table->string('name');
            $table->json('config'); // The JSON configuration for the condition/action/delay
            $table->unsignedBigInteger('next_step_id')->nullable(); // ID of the next step to execute on success
            $table->unsignedBigInteger('fallback_step_id')->nullable(); // ID of the next step to execute on failure (for conditions/branching)
            $table->integer('order_index')->default(0); // for initial sorting/display
            $table->timestamps();
            
            // Note: foreign keys to self (next_step_id, fallback_step_id) can be added as constraints but might cause circular reference issues during seeding, so we'll leave them as just unsignedBigInteger
        });

        Schema::create('hq_workflow_runs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hq_workflow_id')->constrained('hq_workflows')->onDelete('cascade');
            $table->foreignId('tenant_id')->nullable()->constrained('hq_tenants')->onDelete('cascade');
            $table->unsignedBigInteger('current_step_id')->nullable(); // ID of the current step being executed
            $table->string('status')->default('pending'); // pending, running, completed, failed, timeout
            $table->json('payload')->nullable(); // Original event payload/variables
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->text('error_message')->nullable();
            $table->integer('retry_count')->default(0);
            $table->timestamps();
        });

        Schema::create('hq_workflow_executions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hq_workflow_run_id')->constrained('hq_workflow_runs')->onDelete('cascade');
            $table->foreignId('hq_workflow_step_id')->constrained('hq_workflow_steps')->onDelete('cascade');
            $table->string('status')->default('pending'); // pending, running, success, failed, skipped
            $table->json('input_data')->nullable(); // State before step
            $table->json('output_data')->nullable(); // State/Result after step
            $table->text('error_message')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('hq_workflow_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hq_workflow_run_id')->constrained('hq_workflow_runs')->onDelete('cascade');
            $table->foreignId('hq_workflow_execution_id')->nullable()->constrained('hq_workflow_executions')->onDelete('set null');
            $table->string('level')->default('info'); // info, warning, error
            $table->text('message');
            $table->json('context')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hq_workflow_logs');
        Schema::dropIfExists('hq_workflow_executions');
        Schema::dropIfExists('hq_workflow_runs');
        Schema::dropIfExists('hq_workflow_steps');
        Schema::dropIfExists('hq_workflows');
    }
};
