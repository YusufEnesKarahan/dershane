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
        Schema::dropIfExists('hq_user_sessions');
        Schema::dropIfExists('hq_users_security');

        Schema::create('hq_users_security', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('tenant_id')->nullable()->constrained('hq_tenants')->onDelete('cascade');
            $table->timestamp('last_login_at')->nullable();
            $table->ipAddress('last_ip')->nullable();
            $table->integer('failed_attempts')->default(0);
            $table->timestamp('locked_until')->nullable();
            $table->boolean('mfa_enabled')->default(false);
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->unique(['user_id', 'tenant_id']);
        });

        Schema::create('hq_user_sessions', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('tenant_id')->nullable()->constrained('hq_tenants')->onDelete('cascade');
            $table->string('token_hash')->unique();
            $table->string('device')->nullable();
            $table->string('ip')->nullable();
            $table->timestamp('last_activity_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::dropIfExists('hq_login_attempts');
        Schema::create('hq_login_attempts', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade'); // nullable because an attempt might be for non-existent user
            $table->foreignId('tenant_id')->nullable()->constrained('hq_tenants')->onDelete('cascade');
            $table->string('ip')->nullable();
            $table->boolean('success')->default(false);
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hq_login_attempts');
        Schema::dropIfExists('hq_user_sessions');
        Schema::dropIfExists('hq_users_security');
    }
};
