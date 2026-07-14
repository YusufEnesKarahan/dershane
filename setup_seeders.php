<?php

function writeSeeder($name, $content) {
    file_put_contents(__DIR__ . '/database/seeders/' . $name . '.php', $content);
}

writeSeeder('RolesAndPermissionsSeeder', "<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\{Role, Permission, User};

class RolesAndPermissionsSeeder extends Seeder {
    public function run(): void {
        \$adminRole = Role::create(['name' => 'Admin']);
        \$teacherRole = Role::create(['name' => 'Teacher']);
        \$studentRole = Role::create(['name' => 'Student']);

        \$adminUser = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@dershane.com',
            'password' => bcrypt('password'),
        ]);
        \$adminUser->roles()->attach(\$adminRole);
    }
}
");

writeSeeder('BranchSeeder', "<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Branch;

class BranchSeeder extends Seeder {
    public function run(): void {
        Branch::create([
            'name' => 'Kadıköy Merkez Şube',
            'slug' => 'kadikoy-merkez',
            'phone' => '+90 216 555 12 34',
            'email' => 'kadikoy@dershane.com',
            'address' => 'Caddebostan Mah. Kadıköy/İstanbul'
        ]);
        Branch::create([
            'name' => 'Beşiktaş Şube',
            'slug' => 'besiktas',
            'phone' => '+90 212 555 12 34',
            'email' => 'besiktas@dershane.com',
            'address' => 'Barbaros Bulvarı Beşiktaş/İstanbul'
        ]);
    }
}
");

writeSeeder('DemoContentSeeder', "<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\{Page, BlogCategory, Blog, Event, Announcement};

class DemoContentSeeder extends Seeder {
    public function run(): void {
        Page::create(['title' => 'Hakkımızda', 'slug' => 'hakkimizda', 'content' => 'Kurumumuz 2010 yılında kurulmuştur...', 'is_published' => true]);
        Page::create(['title' => 'İletişim', 'slug' => 'iletisim', 'content' => 'Bize ulaşın...', 'is_published' => true]);

        \$cat = BlogCategory::create(['name' => 'Rehberlik', 'slug' => 'rehberlik', 'description' => 'Sınav stratejileri ve rehberlik']);
        
        Blog::create([
            'title' => 'YKS Son 3 Ay Çalışma Stratejisi',
            'slug' => 'yks-son-3-ay-stratejisi',
            'content' => 'Netlerinizi artıracak taktikler...',
            'category_id' => \$cat->id,
            'is_published' => true,
            'published_at' => now()
        ]);

        Event::create([
            'title' => 'YKS Motivasyon Semineri',
            'slug' => 'yks-motivasyon-semineri',
            'description' => 'Sınav kaygısını azaltma.',
            'event_date' => now()->addDays(10),
            'location' => 'Kadıköy Merkez Şube'
        ]);

        Announcement::create([
            'title' => 'Erken Kayıt Dönemi Başladı',
            'slug' => 'erken-kayit-donemi-basladi',
            'content' => 'Erken kayıt fırsatlarını kaçırmayın.',
            'is_active' => true,
            'published_at' => now()
        ]);
    }
}
");

writeSeeder('DemoEducationSeeder', "<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\{Teacher, Course, Classroom, User, Branch};

class DemoEducationSeeder extends Seeder {
    public function run(): void {
        \$branch = Branch::first();
        \$user = User::factory()->create(['name' => 'Kemal Yıldız', 'email' => 'kemal@dershane.com']);
        Teacher::create([
            'user_id' => \$user->id,
            'branch_id' => \$branch->id,
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
            'branch_id' => \$branch->id,
            'is_active' => true
        ]);

        Classroom::create([
            'name' => '12-A Sayısal',
            'branch_id' => \$branch->id,
            'capacity' => 12
        ]);
    }
}
");

writeSeeder('DemoCrmSeeder', "<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\{Lead, ContactMessage};

class DemoCrmSeeder extends Seeder {
    public function run(): void {
        Lead::create([
            'name' => 'Veli Yılmaz',
            'phone' => '05321234567',
            'email' => 'veli@test.com',
            'source' => 'web',
            'status' => 'new'
        ]);

        ContactMessage::create([
            'name' => 'Ayşe Demir',
            'email' => 'ayse@test.com',
            'phone' => '05559876543',
            'subject' => 'Kayıt Bilgisi',
            'message' => 'LGS kursunuz hakkında bilgi almak istiyorum.'
        ]);
    }
}
");

writeSeeder('DatabaseSeeder', "<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        \$this->call([
            RolesAndPermissionsSeeder::class,
            BranchSeeder::class,
            DemoContentSeeder::class,
        ]);

        // Respect Edition logic
        if (edition()->has('education_module')) {
            \$this->call(DemoEducationSeeder::class);
        }

        if (edition()->has('crm_module')) {
            \$this->call(DemoCrmSeeder::class);
        }
    }
}
");

echo "Seeders written successfully.\n";
