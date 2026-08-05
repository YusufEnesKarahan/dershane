<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('exam_branch_results')) {
            Schema::create('exam_branch_results', function (Blueprint $table) {
                $table->id();
                $table->foreignId('exam_result_id')->constrained('exam_results')->cascadeOnDelete();
                $table->string('branch_name'); // Türkçe, Matematik, Fen, Sosyal, Geometri, Fizik, Kimya, Biyoloji, Tarih, Coğrafya, Din, Felsefe, İngilizce
                $table->integer('correct_count')->default(0);
                $table->integer('wrong_count')->default(0);
                $table->integer('empty_count')->default(0);
                $table->decimal('net_count', 8, 2)->default(0.00);
                $table->timestamps();

                $table->unique(['exam_result_id', 'branch_name']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_branch_results');
    }
};
