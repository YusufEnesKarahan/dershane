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
        Schema::create('hq_extensions', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('vendor');
            $table->string('version')->default('1.0.0');
            $table->string('status')->default('active'); // active, deprecated, beta
            $table->string('type')->default('plugin'); // plugin, integration, theme
            $table->json('metadata')->nullable();
            $table->timestamp('installed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('hq_extension_versions', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('extension_id')->constrained('hq_extensions')->onDelete('cascade');
            $table->string('version');
            $table->text('release_notes')->nullable();
            $table->json('requirements')->nullable(); // e.g. {"php": ">=8.1", "hq_central": ">=8.8"}
            $table->json('dependencies')->nullable(); // e.g. {"payment-gateway": ">=2.0"}
            $table->string('status')->default('stable'); // stable, rc, beta, yanked
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('hq_extension_installations', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('extension_id')->constrained('hq_extensions')->onDelete('cascade');
            $table->foreignId('tenant_id')->nullable()->constrained('hq_tenants')->onDelete('cascade');
            $table->foreignId('version_id')->constrained('hq_extension_versions')->onDelete('cascade');
            $table->string('status')->default('installed'); // installed, activated, disabled, updating, removed, failed
            $table->timestamp('enabled_at')->nullable();
            $table->timestamp('disabled_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('hq_extension_permissions', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('extension_id')->constrained('hq_extensions')->onDelete('cascade');
            // Assuming permissions table is named `permissions` or similar in IAM. We will use string identifiers.
            $table->string('permission_key'); // e.g., 'billing.view', 'users.manage'
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('hq_extension_configs', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('extension_id')->constrained('hq_extensions')->onDelete('cascade');
            $table->foreignId('tenant_id')->nullable()->constrained('hq_tenants')->onDelete('cascade');
            $table->json('configuration')->nullable(); // Extension specific runtime config
            $table->timestamps();
            
            $table->unique(['extension_id', 'tenant_id']);
        });

        Schema::create('hq_extension_events', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('extension_id')->constrained('hq_extensions')->onDelete('cascade');
            $table->string('event_name'); // e.g. 'ExtensionInstalled', 'ExtensionActivated'
            $table->json('payload')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hq_extension_events');
        Schema::dropIfExists('hq_extension_configs');
        Schema::dropIfExists('hq_extension_permissions');
        Schema::dropIfExists('hq_extension_installations');
        Schema::dropIfExists('hq_extension_versions');
        Schema::dropIfExists('hq_extensions');
    }
};
