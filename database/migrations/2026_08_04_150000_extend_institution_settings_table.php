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
        Schema::table('institution_settings', function (Blueprint $table) {
            $table->string('favicon')->nullable()->after('logo');
            $table->string('tax_number')->nullable()->after('website');
            $table->text('description')->nullable()->after('tax_number');
            $table->string('primary_color')->default('#4f46e5')->after('description');
            $table->string('secondary_color')->default('#0f172a')->after('primary_color');
            $table->string('timezone')->default('Europe/Istanbul')->after('secondary_color');
            $table->string('language')->default('tr')->after('timezone');
            $table->json('notification_preferences')->nullable()->after('language');
            $table->json('invoice_information')->nullable()->after('notification_preferences');
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('institution_settings', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->dropColumn([
                'favicon',
                'tax_number',
                'description',
                'primary_color',
                'secondary_color',
                'timezone',
                'language',
                'notification_preferences',
                'invoice_information',
            ]);
        });
    }
};
