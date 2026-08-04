<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Domain\Package\Models\Package;
use App\Domain\Package\Models\Feature;

class PackageSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Package V1 — Başlangıç (STARTER)
        $v1 = Package::updateOrCreate(
            ['code' => 'V1'],
            [
                'name' => 'Paket V1 — Başlangıç',
                'description' => 'Basit dijital dershane tanıtımı ve temel öğrenci/öğretmen/sınıf yönetimi.',
                'price_yearly' => 12000.00,
                'price_3_year' => 30000.00,
                'status' => 'active',
            ]
        );

        $v1Features = Feature::whereIn('code', [
            'student',
            'teacher',
            'classroom',
            'schedule',
        ])->pluck('id');
        $v1->features()->sync($v1Features);

        // 2. Package V2 — Profesyonel (PROFESSIONAL)
        $v2 = Package::updateOrCreate(
            ['code' => 'V2'],
            [
                'name' => 'Paket V2 — Profesyonel',
                'description' => 'Yoklama, sınav, ödev ve bildirim destekli gelişmiş operasyonel dershane paketi.',
                'price_yearly' => 24000.00,
                'price_3_year' => 60000.00,
                'status' => 'active',
            ]
        );

        $v2Features = Feature::whereIn('code', [
            'student',
            'teacher',
            'classroom',
            'schedule',
            'attendance',
            'exam',
            'homework',
            'notification',
        ])->pluck('id');
        $v2->features()->sync($v2Features);

        // 3. Package V3 — Enterprise (ENTERPRISE)
        $v3 = Package::updateOrCreate(
            ['code' => 'V3'],
            [
                'name' => 'Paket V3 — Enterprise',
                'description' => 'Rehberlik motoru, finans ve analitik raporlar dahil tüm modülleri içeren sınırsız paket.',
                'price_yearly' => 45000.00,
                'price_3_year' => 110000.00,
                'status' => 'active',
            ]
        );

        $allFeatures = Feature::pluck('id');
        $v3->features()->sync($allFeatures);
    }
}
