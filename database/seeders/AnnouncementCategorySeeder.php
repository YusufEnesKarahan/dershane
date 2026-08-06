<?php

namespace Database\Seeders;

use App\Models\AnnouncementCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AnnouncementCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Akademik', 'color' => 'indigo', 'icon' => 'fa-graduation-cap'],
            ['name' => 'Sınav', 'color' => 'blue', 'icon' => 'fa-file-alt'],
            ['name' => 'Duyuru', 'color' => 'emerald', 'icon' => 'fa-bullhorn'],
            ['name' => 'Etkinlik', 'color' => 'purple', 'icon' => 'fa-calendar-alt'],
            ['name' => 'Tatil', 'color' => 'amber', 'icon' => 'fa-sun'],
            ['name' => 'Finans', 'color' => 'rose', 'icon' => 'fa-coins'],
            ['name' => 'Genel', 'color' => 'neutral', 'icon' => 'fa-info-circle'],
        ];

        foreach ($categories as $cat) {
            AnnouncementCategory::firstOrCreate(
                ['slug' => Str::slug($cat['name'])],
                [
                    'name' => $cat['name'],
                    'color' => $cat['color'],
                    'icon' => $cat['icon'],
                ]
            );
        }
    }
}
