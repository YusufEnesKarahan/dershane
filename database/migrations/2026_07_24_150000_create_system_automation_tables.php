<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('job_histories', function (Blueprint $table): void { $table->id(); $table->string('job_name')->index(); $table->string('status')->index(); $table->json('payload')->nullable(); $table->timestamp('started_at')->nullable()->index(); $table->timestamp('completed_at')->nullable(); $table->text('error_message')->nullable(); $table->timestamps(); });
        Schema::create('automation_logs', function (Blueprint $table): void { $table->id(); $table->string('job_name')->index(); $table->string('status')->index(); $table->json('payload')->nullable(); $table->timestamp('started_at')->nullable()->index(); $table->timestamp('completed_at')->nullable(); $table->text('error_message')->nullable(); $table->timestamps(); });
    }
    public function down(): void { Schema::dropIfExists('automation_logs'); Schema::dropIfExists('job_histories'); }
};
