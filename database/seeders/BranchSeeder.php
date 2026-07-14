<?php
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
