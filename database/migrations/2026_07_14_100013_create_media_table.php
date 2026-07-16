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
        Schema::create('media', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('disk')->default('public');
            $table->string('directory')->nullable();
            $table->string('filename');
            $table->string('original_name');
            $table->string('extension');
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('size');
            $table->integer('width')->nullable();
            $table->integer('height')->nullable();
            $table->string('checksum')->index(); // SHA256 detection
            $table->string('alt')->nullable();
            $table->string('caption')->nullable();
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->string('visibility')->default('public');
            $table->string('collection')->default('general');
            $table->foreignId('folder_id')->nullable()->constrained('media_folders')->nullOnDelete();
            $table->string('status')->default('active');
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('last_used_at')->nullable();
            $table->integer('usage_count')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('media');
    }
};
