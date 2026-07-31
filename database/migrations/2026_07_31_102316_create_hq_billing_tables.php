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
        Schema::create('hq_plans', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('type')->default('subscription'); // subscription, usage, hybrid
            $table->decimal('price', 10, 2)->default(0.00);
            $table->string('billing_period')->default('monthly'); // monthly, yearly
            $table->json('limits')->nullable();
            $table->json('features')->nullable();
            $table->string('status')->default('active'); // active, inactive
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('hq_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('tenant_id')->constrained('hq_tenants')->onDelete('cascade');
            $table->foreignId('plan_id')->constrained('hq_plans')->onDelete('cascade');
            $table->string('status')->default('active'); // active, cancelled, expired, pending, past_due
            $table->timestamp('starts_at')->useCurrent();
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('hq_subscription_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_id')->constrained('hq_subscriptions')->onDelete('cascade');
            $table->foreignId('extension_id')->nullable()->constrained('hq_extensions')->onDelete('set null');
            $table->integer('quantity')->default(1);
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        Schema::create('hq_usage_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('hq_tenants')->onDelete('cascade');
            $table->string('metric_name');
            $table->decimal('value', 12, 4)->default(0);
            $table->string('period'); // YYYY-MM
            $table->timestamps();
            $table->unique(['tenant_id', 'metric_name', 'period']);
        });

        Schema::create('hq_entitlements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('hq_tenants')->onDelete('cascade');
            $table->string('feature_key');
            $table->string('limit_value')->nullable(); // could be numeric or boolean (true/false)
            $table->string('source')->default('plan'); // plan, extension, custom
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
            
            $table->unique(['tenant_id', 'feature_key', 'source']);
        });

        Schema::create('hq_invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('hq_tenants')->onDelete('cascade');
            $table->foreignId('subscription_id')->nullable()->constrained('hq_subscriptions')->onDelete('set null');
            $table->decimal('amount', 10, 2)->default(0.00);
            $table->string('status')->default('pending'); // pending, paid, failed, cancelled, refunded
            $table->string('invoice_number')->unique();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('hq_payment_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('hq_tenants')->onDelete('cascade');
            $table->string('provider'); // mock, stripe, iyzico
            $table->string('event_type'); // payment_succeeded, payment_failed
            $table->json('payload')->nullable();
            $table->string('status')->default('processed'); // processed, failed, pending
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hq_payment_events');
        Schema::dropIfExists('hq_invoices');
        Schema::dropIfExists('hq_entitlements');
        Schema::dropIfExists('hq_usage_records');
        Schema::dropIfExists('hq_subscription_items');
        Schema::dropIfExists('hq_subscriptions');
        Schema::dropIfExists('hq_plans');
    }
};
