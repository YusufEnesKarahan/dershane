<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Domain\Package\Models\Feature;

class FeatureSeeder extends Seeder
{
    public function run(): void
    {
        $features = [
            [
                'name' => 'Öğrenci Yönetimi',
                'code' => 'student',
                'description' => 'Öğrenci kayıt, profil ve sınıf atama yönetimi.',
                'module' => 'Student',
                'status' => 'active'
            ],
            [
                'name' => 'Öğretmen Yönetimi',
                'code' => 'teacher',
                'description' => 'Öğretmen tanımlama ve yetkilendirme işlemleri.',
                'module' => 'Teacher',
                'status' => 'active'
            ],
            [
                'name' => 'Sınıf & Ders Yönetimi',
                'code' => 'classroom',
                'description' => 'Sınıf, şube ve ders tanımlama yönetimi.',
                'module' => 'Classroom',
                'status' => 'active'
            ],
            [
                'name' => 'Ders Programı',
                'code' => 'schedule',
                'description' => 'Sınıf ve öğretmen haftalık ders programı planlaması.',
                'module' => 'Schedule',
                'status' => 'active'
            ],
            [
                'name' => 'Yoklama Sistemi',
                'code' => 'attendance',
                'description' => 'Günlük ve ders bazlı öğrenci devamsızlık takibi.',
                'module' => 'Attendance',
                'status' => 'active'
            ],
            [
                'name' => 'Sınav Yönetimi',
                'code' => 'exam',
                'description' => 'Sınav tanımlama, optik/sonuç girişi ve net hesaplama.',
                'module' => 'Exam',
                'status' => 'active'
            ],
            [
                'name' => 'Ödev Yönetimi',
                'code' => 'homework',
                'description' => 'Ödev verme, teslim takibi ve değerlendirme.',
                'module' => 'Homework',
                'status' => 'active'
            ],
            [
                'name' => 'Bildirim Merkezi',
                'code' => 'notification',
                'description' => 'Kurum içi bildirim, mesajlaşma ve duyuru gönderimi.',
                'module' => 'Notification',
                'status' => 'active'
            ],
            [
                'name' => 'Rehberlik & Performans',
                'code' => 'guidance',
                'description' => 'Öğrenci risk analizi, görüşme takibi ve performans motoru.',
                'module' => 'Guidance',
                'status' => 'active'
            ],
            [
                'name' => 'Finans Yönetimi',
                'code' => 'finance',
                'description' => 'Taksit, ödeme planı, indirim ve iade yönetimi.',
                'module' => 'Finance',
                'status' => 'active'
            ],
            [
                'name' => 'Gelişmiş Raporlar',
                'code' => 'reports',
                'description' => 'Kurum bazlı analitik, akademik ve finansal raporlar.',
                'module' => 'Reports',
                'status' => 'active'
            ],
        ];

        foreach ($features as $featureData) {
            Feature::updateOrCreate(
                ['code' => $featureData['code']],
                $featureData
            );
        }
    }
}
