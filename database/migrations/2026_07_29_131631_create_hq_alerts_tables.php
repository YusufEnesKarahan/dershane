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
        Schema::create('hq_alert_rules', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name');
            $table->string('category')->index();
            $table->string('severity')->index(); // info, warning, danger, critical
            $table->string('event_type')->index();
            $table->json('condition')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('cooldown_minutes')->default(0);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
        });

        Schema::create('hq_alerts', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('rule_id')->nullable()->constrained('hq_alert_rules')->nullOnDelete();
            $table->unsignedBigInteger('tenant_id')->nullable();
            $table->unsignedBigInteger('system_instance_id')->nullable();
            
            $table->string('title');
            $table->text('message');
            $table->string('severity')->index();
            $table->string('status')->default('open')->index(); // open, acknowledged, resolved
            
            $table->timestamp('triggered_at')->useCurrent();
            $table->timestamp('resolved_at')->nullable();
            
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            $table->foreign('tenant_id')->references('id')->on('hq_tenants')->nullOnDelete();
            $table->foreign('system_instance_id')->references('id')->on('hq_system_instances')->nullOnDelete();
            
            $table->index(['created_at']);
        });

        Schema::create('hq_notification_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('alert_id')->constrained('hq_alerts')->cascadeOnDelete();
            $table->string('channel'); // database, mail, webhook
            $table->string('recipient')->nullable();
            $table->string('status')->default('pending'); // pending, sent, failed
            $table->timestamp('sent_at')->nullable();
            $table->text('error_message')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hq_alerts_tables');
    }
};
