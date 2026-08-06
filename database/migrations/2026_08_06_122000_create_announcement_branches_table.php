<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('announcement_branches')) {
            Schema::create('announcement_branches', function (Blueprint $table) {
                $table->id();
                $table->foreignId('announcement_id')->constrained('announcements')->cascadeOnDelete();
                $table->foreignId('branch_id')->constrained('branches')->cascadeOnDelete();
                $table->timestamps();

                $table->unique(['announcement_id', 'branch_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('announcement_branches');
    }
};
