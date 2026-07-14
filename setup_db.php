<?php

$migrationsDir = __DIR__ . '/database/migrations/';

// Clean old migrations (excluding Laravel defaults if needed, but we'll just delete our own patterns)
$files = glob($migrationsDir . '2026_*_create_*_table.php');
foreach ($files as $file) {
    unlink($file);
}

$counter = 100000;

function writeMigration($tableName, $schemaString) {
    global $migrationsDir, $counter;
    $date = date('Y_m_d_') . $counter;
    $counter++;
    $filename = $date . '_create_' . $tableName . '_table.php';
    $file = $migrationsDir . $filename;
    
    $content = "<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('$tableName', function (Blueprint \$table) {
            \$table->id();
$schemaString
            \$table->timestamps();
            \$table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('$tableName');
    }
};
";
    file_put_contents($file, $content);
}

function writePivotMigration($tableName, $schemaString) {
    global $migrationsDir, $counter;
    $date = date('Y_m_d_') . $counter;
    $counter++;
    $filename = $date . '_create_' . $tableName . '_table.php';
    $content = "<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('$tableName', function (Blueprint \$table) {
$schemaString
        });
    }
    public function down(): void {
        Schema::dropIfExists('$tableName');
    }
};";
    file_put_contents($migrationsDir . $filename, $content);
}

// 1. Independent Tables
writeMigration('roles', "
            \$table->string('name')->unique();
            \$table->string('guard_name')->default('web');
");

writeMigration('permissions', "
            \$table->string('name')->unique();
            \$table->string('guard_name')->default('web');
");

writeMigration('branches', "
            \$table->string('name')->unique();
            \$table->string('slug')->unique();
            \$table->string('phone')->nullable();
            \$table->string('email')->nullable();
            \$table->text('address')->nullable();
            \$table->foreignId('manager_id')->nullable()->constrained('users')->nullOnDelete();
");

writeMigration('blog_categories', "
            \$table->string('name');
            \$table->string('slug')->unique();
            \$table->text('description')->nullable();
");

writeMigration('documents', "
            \$table->string('title');
            \$table->text('description')->nullable();
            \$table->string('file_path');
            \$table->string('type')->index();
");

writeMigration('settings', "
            \$table->string('key')->unique();
            \$table->text('value')->nullable();
            \$table->string('group')->index();
");

writeMigration('pages', "
            \$table->string('title');
            \$table->string('slug')->unique();
            \$table->longText('content')->nullable();
            \$table->boolean('is_published')->default(false)->index();
            \$table->string('meta_title')->nullable();
            \$table->text('meta_description')->nullable();
");

writeMigration('sliders', "
            \$table->string('title');
            \$table->string('image_path');
            \$table->string('link')->nullable();
            \$table->integer('order')->default(0)->index();
            \$table->boolean('is_active')->default(true)->index();
");

writeMigration('events', "
            \$table->string('title');
            \$table->string('slug')->unique();
            \$table->text('description')->nullable();
            \$table->dateTime('event_date')->index();
            \$table->string('location')->nullable();
            \$table->string('image_path')->nullable();
");

writeMigration('announcements', "
            \$table->string('title');
            \$table->string('slug')->unique();
            \$table->text('content');
            \$table->boolean('is_active')->default(true)->index();
            \$table->timestamp('published_at')->nullable();
");

writeMigration('galleries', "
            \$table->string('title');
            \$table->text('description')->nullable();
            \$table->string('image_path');
            \$table->integer('order')->default(0)->index();
            \$table->boolean('is_active')->default(true)->index();
");

writeMigration('leads', "
            \$table->string('name');
            \$table->string('phone');
            \$table->string('email')->nullable();
            \$table->string('source')->nullable()->index();
            \$table->string('status')->default('new')->index();
            \$table->text('notes')->nullable();
");

writeMigration('contact_messages', "
            \$table->string('name');
            \$table->string('email')->nullable();
            \$table->string('phone')->nullable();
            \$table->string('subject')->nullable();
            \$table->text('message');
            \$table->boolean('is_read')->default(false)->index();
");

writeMigration('media', "
            \$table->morphs('model');
            \$table->string('collection_name');
            \$table->string('name');
            \$table->string('file_name');
            \$table->string('mime_type')->nullable();
            \$table->string('disk');
            \$table->unsignedBigInteger('size');
            \$table->json('manipulations');
            \$table->json('custom_properties');
            \$table->json('generated_conversions');
            \$table->json('responsive_images');
            \$table->unsignedInteger('order_column')->nullable()->index();
");

// 2. Dependent Tables
writeMigration('blogs', "
            \$table->string('title');
            \$table->string('slug')->unique();
            \$table->longText('content');
            \$table->foreignId('category_id')->nullable()->constrained('blog_categories')->nullOnDelete();
            \$table->foreignId('author_id')->nullable()->constrained('users')->nullOnDelete();
            \$table->string('image_path')->nullable();
            \$table->boolean('is_published')->default(false)->index();
            \$table->timestamp('published_at')->nullable();
");

writeMigration('teachers', "
            \$table->foreignId('user_id')->constrained()->cascadeOnDelete();
            \$table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            \$table->string('title')->nullable();
            \$table->text('bio')->nullable();
            \$table->string('specialities')->nullable();
");

writeMigration('courses', "
            \$table->string('name');
            \$table->string('slug')->unique();
            \$table->text('description')->nullable();
            \$table->decimal('price', 10, 2)->nullable();
            \$table->string('duration')->nullable();
            \$table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            \$table->boolean('is_active')->default(true)->index();
");

writeMigration('classrooms', "
            \$table->string('name');
            \$table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            \$table->integer('capacity')->default(12);
");

writeMigration('registrations', "
            \$table->string('student_name');
            \$table->string('student_phone')->nullable();
            \$table->string('parent_name');
            \$table->string('parent_phone');
            \$table->string('grade')->index();
            \$table->string('program')->index();
            \$table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            \$table->string('status')->default('pending')->index();
");

writePivotMigration('role_user', "
            \$table->foreignId('role_id')->constrained()->cascadeOnDelete();
            \$table->foreignId('user_id')->constrained()->cascadeOnDelete();
            \$table->unique(['role_id', 'user_id']);
");

writePivotMigration('permission_role', "
            \$table->foreignId('permission_id')->constrained()->cascadeOnDelete();
            \$table->foreignId('role_id')->constrained()->cascadeOnDelete();
            \$table->unique(['permission_id', 'role_id']);
");

echo "All migrations created perfectly.\n";
