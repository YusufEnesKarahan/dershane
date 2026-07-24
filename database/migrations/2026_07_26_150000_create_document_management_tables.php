<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('color')->default('#0d9488');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Extend existing documents table with Document Management fields
        Schema::table('documents', function (Blueprint $table) {
            $table->nullableMorphs('documentable');
            $table->foreignId('category_id')->nullable()->constrained('document_categories')->onDelete('set null');
            $table->string('file_name')->nullable()->after('file_path');
            $table->string('file_type')->nullable()->after('file_name');
            $table->unsignedBigInteger('file_size')->default(0)->after('file_type');
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->onDelete('set null');
            $table->string('status')->default('active');
        });

        Schema::create('document_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained('documents')->onDelete('cascade');
            $table->integer('version_number')->default(1);
            $table->string('file_path');
            $table->foreignId('uploaded_by')->constrained('users')->onDelete('cascade');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('document_permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained('documents')->onDelete('cascade');
            $table->foreignId('role_id')->nullable()->constrained('roles')->onDelete('cascade');
            $table->boolean('can_view')->default(true);
            $table->boolean('can_download')->default(true);
            $table->boolean('can_delete')->default(false);
            $table->timestamps();
        });

        Schema::create('document_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained('documents')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('action'); // upload, update, delete, download, view, restore
            $table->string('ip_address')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_logs');
        Schema::dropIfExists('document_permissions');
        Schema::dropIfExists('document_versions');

        Schema::table('documents', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropForeign(['uploaded_by']);
            $table->dropMorphs('documentable');
            $table->dropColumn(['category_id', 'file_name', 'file_type', 'file_size', 'uploaded_by', 'status']);
        });

        Schema::dropIfExists('document_categories');
    }
};
