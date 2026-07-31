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
        Schema::dropIfExists('hq_configuration_rollbacks');
        Schema::dropIfExists('hq_secret_versions');
        Schema::dropIfExists('hq_secret_vaults');
        Schema::dropIfExists('hq_environment_profiles');
        Schema::dropIfExists('hq_feature_flag_targets');
        Schema::dropIfExists('hq_feature_flags');
        Schema::dropIfExists('hq_configuration_changes');
        Schema::dropIfExists('hq_configuration_versions');
        Schema::dropIfExists('hq_configurations');
        Schema::dropIfExists('hq_configuration_groups');
        
        Schema::create('hq_configuration_groups', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('hq_configurations', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('group_id')->constrained('hq_configuration_groups')->onDelete('cascade');
            $table->foreignId('tenant_id')->nullable()->constrained('hq_tenants')->onDelete('cascade');
            $table->string('key')->index(); // Config key e.g. "theme.color"
            $table->json('value')->nullable(); // JSON to support string, array, int
            $table->string('type')->default('string'); // string, boolean, integer, json
            $table->boolean('is_encrypted')->default(false);
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // Allow tenant overrides for the same key
            $table->unique(['group_id', 'tenant_id', 'key']);
        });

        Schema::create('hq_configuration_versions', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('configuration_id')->constrained('hq_configurations')->onDelete('cascade');
            $table->string('version_tag')->index(); // e.g. "v1.2.3"
            $table->json('value');
            $table->string('created_by')->nullable(); // user id or system
            $table->timestamps();
        });

        Schema::create('hq_configuration_changes', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('configuration_id')->constrained('hq_configurations')->onDelete('cascade');
            $table->json('old_value')->nullable();
            $table->json('new_value')->nullable();
            $table->string('changed_by')->nullable();
            $table->timestamps();
        });

        Schema::create('hq_feature_flags', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name');
            $table->string('key')->unique();
            $table->text('description')->nullable();
            $table->boolean('is_enabled')->default(false); // Master switch
            $table->json('rules')->nullable(); // Recursive rules (all, any, etc)
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('hq_feature_flag_targets', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('feature_flag_id')->constrained('hq_feature_flags')->onDelete('cascade');
            $table->string('target_type'); // 'tenant', 'user', 'role', 'region'
            $table->string('target_id');
            $table->boolean('is_enabled')->default(true); // Override for specific target
            $table->timestamps();
        });

        Schema::create('hq_environment_profiles', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name'); // 'Production', 'Staging', etc
            $table->string('slug')->unique();
            $table->json('overrides')->nullable(); // Specific configs to override in this profile
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('hq_secret_vaults', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name');
            $table->string('key')->unique();
            $table->text('encrypted_value');
            $table->text('description')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->string('rotation_interval')->nullable(); // e.g. "30 days"
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('hq_secret_versions', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('secret_vault_id')->constrained('hq_secret_vaults')->onDelete('cascade');
            $table->text('encrypted_value');
            $table->string('created_by')->nullable();
            $table->timestamps();
        });

        Schema::create('hq_configuration_rollbacks', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('configuration_id')->constrained('hq_configurations')->onDelete('cascade');
            $table->foreignId('version_id')->constrained('hq_configuration_versions')->onDelete('cascade');
            $table->json('from_value')->nullable();
            $table->json('to_value')->nullable();
            $table->string('executed_by')->nullable();
            $table->timestamp('rolled_back_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hq_configuration_rollbacks');
        Schema::dropIfExists('hq_secret_versions');
        Schema::dropIfExists('hq_secret_vaults');
        Schema::dropIfExists('hq_environment_profiles');
        Schema::dropIfExists('hq_feature_flag_targets');
        Schema::dropIfExists('hq_feature_flags');
        Schema::dropIfExists('hq_configuration_changes');
        Schema::dropIfExists('hq_configuration_versions');
        Schema::dropIfExists('hq_configurations');
        Schema::dropIfExists('hq_configuration_groups');
    }
};
