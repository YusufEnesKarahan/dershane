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
        Schema::table('pages', function (Blueprint $table) {
            // Drop column is_published to avoid duplication
            $table->dropColumn('is_published');
            
            $table->text('excerpt')->nullable()->after('content');
            $table->string('meta_keywords')->nullable()->after('meta_description');
            $table->string('og_title')->nullable()->after('meta_keywords');
            $table->text('og_description')->nullable()->after('og_title');
            $table->string('og_image')->nullable()->after('og_description');
            $table->string('canonical_url')->nullable()->after('og_image');
            $table->string('robots')->nullable()->after('canonical_url');
            $table->string('template')->nullable()->after('robots');
            $table->string('status')->default('draft')->index()->after('template');
            $table->timestamp('published_at')->nullable()->after('status');
            $table->foreignId('author_id')->nullable()->constrained('users')->nullOnDelete()->after('published_at');
            $table->foreignId('parent_id')->nullable()->constrained('pages')->nullOnDelete()->after('author_id');
            $table->integer('sort_order')->default(0)->after('parent_id');
            $table->boolean('is_homepage')->default(false)->after('sort_order');
            $table->boolean('is_system')->default(false)->after('is_homepage');
            $table->json('revisions')->nullable()->after('is_system');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->boolean('is_published')->default(false)->index()->after('content');
            $table->dropForeign(['author_id']);
            $table->dropForeign(['parent_id']);
            $table->dropColumn([
                'excerpt',
                'meta_keywords',
                'og_title',
                'og_description',
                'og_image',
                'canonical_url',
                'robots',
                'template',
                'status',
                'published_at',
                'author_id',
                'parent_id',
                'sort_order',
                'is_homepage',
                'is_system',
                'revisions'
            ]);
        });
    }
};
