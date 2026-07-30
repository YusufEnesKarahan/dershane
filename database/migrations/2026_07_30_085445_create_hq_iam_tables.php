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
        Schema::create('hq_roles', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('tenant_id')->nullable()->constrained('hq_tenants')->cascadeOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->boolean('is_system')->default(false);
            $table->timestamps();
        });

        Schema::create('hq_permissions', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('module')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('hq_role_permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role_id')->constrained('hq_roles')->cascadeOnDelete();
            $table->foreignId('permission_id')->constrained('hq_permissions')->cascadeOnDelete();
            $table->timestamps();
            
            $table->unique(['role_id', 'permission_id']);
        });

        Schema::create('hq_user_roles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('role_id')->constrained('hq_roles')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['user_id', 'role_id']);
        });

        Schema::create('hq_access_policies', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('tenant_id')->nullable()->constrained('hq_tenants')->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->json('ip_restrictions')->nullable(); // array of IPs/CIDRs
            $table->json('time_restrictions')->nullable(); // schedule objects
            $table->json('resource_restrictions')->nullable(); // module specific limits
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('hq_api_keys', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('tenant_id')->nullable()->constrained('hq_tenants')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->string('name');
            $table->string('token_hash')->unique();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->boolean('is_revoked')->default(false);
            $table->unsignedInteger('usage_count')->default(0);
            $table->timestamps();
        });

        Schema::create('hq_service_accounts', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('tenant_id')->nullable()->constrained('hq_tenants')->cascadeOnDelete();
            $table->string('name');
            $table->string('description')->nullable();
            $table->string('token_hash')->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('hq_mfa_settings', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->boolean('is_enabled')->default(false);
            $table->text('secret')->nullable(); // Encrypted
            $table->json('recovery_codes')->nullable(); // Hashed array
            $table->json('backup_codes')->nullable();
            $table->timestamps();
        });

        Schema::create('hq_login_attempts', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->boolean('is_successful')->default(false);
            $table->timestamp('attempted_at')->useCurrent();
            $table->json('context')->nullable();
            $table->timestamps();
        });

        Schema::create('hq_security_sessions', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('session_token')->unique(); // Hashed
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('last_activity')->useCurrent();
            $table->timestamp('expires_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hq_security_sessions');
        Schema::dropIfExists('hq_login_attempts');
        Schema::dropIfExists('hq_mfa_settings');
        Schema::dropIfExists('hq_service_accounts');
        Schema::dropIfExists('hq_api_keys');
        Schema::dropIfExists('hq_access_policies');
        Schema::dropIfExists('hq_user_roles');
        Schema::dropIfExists('hq_role_permissions');
        Schema::dropIfExists('hq_permissions');
        Schema::dropIfExists('hq_roles');
    }
};
