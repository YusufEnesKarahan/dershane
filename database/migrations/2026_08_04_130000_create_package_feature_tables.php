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
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique(); // e.g. V1, V2, V3 or STARTER, PROFESSIONAL, ENTERPRISE
            $table->text('description')->nullable();
            $table->decimal('price_yearly', 10, 2)->default(0);
            $table->decimal('price_3_year', 10, 2)->default(0);
            $table->string('status')->default('active'); // active, inactive
            $table->timestamps();
        });

        Schema::create('features', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique(); // e.g. attendance, exam, homework, schedule, notification, guidance, finance, reports
            $table->text('description')->nullable();
            $table->string('module')->nullable();
            $table->string('status')->default('active'); // active, inactive
            $table->timestamps();
        });

        Schema::create('package_features', function (Blueprint $table) {
            $table->foreignId('package_id')->constrained('packages')->cascadeOnDelete();
            $table->foreignId('feature_id')->constrained('features')->cascadeOnDelete();
            $table->primary(['package_id', 'feature_id']);
        });

        Schema::create('branch_packages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches')->cascadeOnDelete();
            $table->foreignId('package_id')->constrained('packages')->cascadeOnDelete();
            $table->string('license_type')->default('yearly'); // yearly, three_year
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->string('status')->default('active'); // active, expired, cancelled
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('branch_packages');
        Schema::dropIfExists('package_features');
        Schema::dropIfExists('features');
        Schema::dropIfExists('packages');
    }
};
