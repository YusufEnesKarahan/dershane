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
        Schema::create('system_identity', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->uuid('installation_uuid')->nullable();
            $table->string('product_name')->default('Dershane ERP');
            $table->string('product_version')->nullable();
            $table->string('license_key')->nullable();
            $table->integer('branch_count')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_identity');
    }
};
