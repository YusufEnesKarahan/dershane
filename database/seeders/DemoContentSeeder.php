<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\{Page, BlogCategory, Blog, Event, Announcement};

class DemoContentSeeder extends Seeder {
    public function run(): void {
        Page::create(['title' => 'Hakkımızda', 'slug' => 'hakkimizda', 'content' => 'Kurumumuz 2010 yılında kurulmuştur...', 'status' => 'published', 'template' => 'about']);
        Page::create(['title' => 'İletişim', 'slug' => 'iletisim', 'content' => 'Bize ulaşın...', 'status' => 'published', 'template' => 'contact']);

        // Seed default folders
        $folders = ['Brand Assets', 'Blog', 'Teachers', 'Courses', 'Gallery', 'Uploads'];
        foreach ($folders as $idx => $name) {
            \App\Models\MediaFolder::firstOrCreate([
                'name' => $name,
                'slug' => \Illuminate\Support\Str::slug($name),
                'order_column' => $idx
            ]);
        }

        $cat = BlogCategory::create(['name' => 'Rehberlik', 'slug' => 'rehberlik', 'description' => 'Sınav stratejileri ve rehberlik']);
        
        Blog::create([
            'title' => 'YKS Son 3 Ay Çalışma Stratejisi',
            'slug' => 'yks-son-3-ay-stratejisi',
            'content' => 'Netlerinizi artıracak taktikler...',
            'category_id' => $cat->id,
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
