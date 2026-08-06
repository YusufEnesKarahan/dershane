<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('announcement_attachments')) {
            Schema::create('announcement_attachments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('announcement_id')->constrained('announcements')->cascadeOnDelete();
                $table->string('file_name');
                $table->string('file_path');
                $table->integer('file_size')->default(0);
                $table->string('file_type')->default('document'); // pdf, word, excel, image, document
                $table->string('mime_type')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('announcement_attachments');
    }
};
