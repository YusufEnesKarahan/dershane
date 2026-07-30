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
        Schema::create('hq_subscription_plans', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('billing_period')->default('monthly'); // monthly, yearly
            $table->decimal('price', 10, 2)->default(0.00);
            $table->string('currency')->default('USD');
            $table->json('limits')->nullable(); // e.g., {"students": 500, "storage": "50GB"}
            $table->json('features')->nullable(); // array of feature keys
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
        });

        Schema::create('hq_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('tenant_id')->constrained('hq_tenants')->cascadeOnDelete();
            $table->foreignId('plan_id')->constrained('hq_subscription_plans')->restrictOnDelete();
            
            $table->string('status')->default('active')->index(); // trial, active, past_due, cancelled, expired
            
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        Schema::create('hq_subscription_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_id')->constrained('hq_subscriptions')->cascadeOnDelete();
            $table->unsignedBigInteger('old_plan_id')->nullable();
            $table->unsignedBigInteger('new_plan_id')->nullable();
            
            $table->string('action'); // created, upgraded, downgraded, cancelled, renewed
            $table->json('metadata')->nullable();
            
            $table->timestamps();
            
            $table->foreign('old_plan_id')->references('id')->on('hq_subscription_plans')->nullOnDelete();
            $table->foreign('new_plan_id')->references('id')->on('hq_subscription_plans')->nullOnDelete();
        });

        Schema::create('hq_invoices', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('tenant_id')->constrained('hq_tenants')->cascadeOnDelete();
            $table->foreignId('subscription_id')->nullable()->constrained('hq_subscriptions')->nullOnDelete();
            
            $table->string('invoice_number')->unique();
            $table->decimal('amount', 10, 2);
            $table->string('currency')->default('USD');
            
            $table->string('status')->default('draft')->index(); // draft, pending, paid, failed, cancelled
            
            $table->timestamp('issued_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        Schema::create('hq_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('hq_invoices')->cascadeOnDelete();
            
            $table->string('provider'); // mock, stripe, iyzico
            $table->string('transaction_id')->nullable();
            
            $table->decimal('amount', 10, 2);
            $table->string('status')->default('pending'); // pending, successful, failed
            
            $table->timestamp('paid_at')->nullable();
            $table->json('metadata')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hq_payments');
        Schema::dropIfExists('hq_invoices');
        Schema::dropIfExists('hq_subscription_history');
        Schema::dropIfExists('hq_subscriptions');
        Schema::dropIfExists('hq_subscription_plans');
    }
};
