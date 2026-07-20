<?php

namespace App\Console\Commands;

use App\Services\HqService;
use Illuminate\Console\Command;

class HqRegisterCommand extends Command
{
    protected $signature = 'hq:register';

    protected $description = 'Registers this Dershane ERP instance with central HQ Panel.';

    public function handle(HqService $hqService): int
    {
        $this->info('🏛️ HQ Panel Kayıt İşlemi Başlatılıyor...');

        $result = $hqService->register();

        if ($result['success']) {
            $this->info('✅ ' . $result['message']);
            $this->line('UUID: ' . ($result['data']['site_uuid'] ?? '-'));
            $this->line('Lisans Durumu: ' . ($result['data']['license_status'] ?? 'active'));
            return Command::SUCCESS;
        }

        $this->error('❌ ' . $result['message']);
        return Command::FAILURE;
    }
}
