<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('pre_registrations')) {
            Schema::create('pre_registrations', function (Blueprint $table) {
                $table->id();
                $table->foreignId('branch_id')->constrained('branches')->cascadeOnDelete();
                $table->string('student_name');
                $table->string('phone');
                $table->string('email')->nullable();
                $table->string('classroom_name')->nullable(); // Sınıf / Seviye
                $table->string('interested_program')->nullable(); // İlgilendiği Program
                $table->string('source')->default('Diğer'); // Instagram, Google, Referans, Web, Telefon, Diğer
                $table->string('status')->default('Yeni'); // Yeni, Arandı, Randevu, Kayıt Oldu, İptal
                $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
                $table->text('notes')->nullable();
                $table->dateTime('reminder_at')->nullable();
                $table->foreignId('converted_student_id')->nullable()->constrained('students')->nullOnDelete();
                $table->timestamps();
                $table->softDeletes();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('pre_registrations');
    }
};
