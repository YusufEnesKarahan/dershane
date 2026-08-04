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
        Schema::create('onboarding_steps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches')->cascadeOnDelete();
            $table->integer('step')->default(1);
            $table->string('status')->default('in_progress'); // in_progress, completed
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('institution_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches')->cascadeOnDelete();
            $table->string('institution_name');
            $table->string('logo')->nullable();
            $table->string('phone');
            $table->string('email');
            $table->text('address');
            $table->string('city')->nullable();
            $table->string('district')->nullable();
            $table->string('website')->nullable();
            $table->string('academic_year')->nullable();
            $table->timestamps();
        });

        Schema::create('onboarding_checklists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches')->cascadeOnDelete();
            $table->string('key');
            $table->boolean('completed')->default(false);
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->unique(['branch_id', 'key']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('onboarding_checklists');
        Schema::dropIfExists('institution_settings');
        Schema::dropIfExists('onboarding_steps');
    }
};
