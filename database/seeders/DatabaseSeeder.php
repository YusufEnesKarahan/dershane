<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            PlatformSettingsSeeder::class,
            RolesAndPermissionsSeeder::class,
            FeatureSeeder::class,
            PackageSeeder::class,
            BranchSeeder::class,
            AnnouncementCategorySeeder::class,
            DemoContentSeeder::class,
        ]);

        // Respect Edition logic
        if (edition()->has('education_module')) {
            $this->call(DemoEducationSeeder::class);
        }

        if (edition()->has('crm_module')) {
            $this->call(DemoCrmSeeder::class);
        }
    }
}
