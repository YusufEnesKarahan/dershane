<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\{Teacher, Course, Classroom, User, Branch};

class DemoEducationSeeder extends Seeder {
    public function run(): void {
        $branch = Branch::first();
        $user = User::factory()->create(['name' => 'Kemal Yıldız', 'email' => 'kemal@dershane.com']);
        Teacher::create([
            'user_id' => $user->id,
            'branch_id' => $branch->id,
            'title' => 'Matematik Zümre Başkanı',
            'bio' => 'Boğaziçi Mezunu, 15+ yıllık tecrübe.',
            'specialities' => 'Matematik, Geometri'
        ]);

        Course::create([
            'name' => 'YKS (TYT-AYT) Hazırlık',
            'slug' => 'yks-hazirlik',
            'description' => 'Yoğunlaştırılmış müfredat...',
            'price' => 25000.00,
            'duration' => '10 Ay',
            'branch_id' => $branch->id,
            'is_active' => true
        ]);

        Classroom::create([
            'name' => '12-A Sayısal',
            'branch_id' => $branch->id,
            'capacity' => 12
        ]);
    }
}
