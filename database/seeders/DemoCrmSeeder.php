<?php
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
