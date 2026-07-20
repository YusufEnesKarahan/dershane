<?php

namespace App\Console\Commands;

use App\Services\HqService;
use Illuminate\Console\Command;

class HqSyncCommand extends Command
{
    protected $signature = 'hq:sync';

    protected $description = 'Synchronizes telemetry, heartbeat, and pending commands with central HQ Panel.';

    public function handle(HqService $hqService): int
    {
        $this->info('🔄 HQ Senkronizasyonu Başlatılıyor...');

        $result = $hqService->sync();

        if ($result['success']) {
            $this->info('✅ HQ Senkronizasyonu Başarılı.');
            $this->line('Lisans Durumu: ' . ($result['license_status'] ?? 'active'));
            
            if (! empty($result['executed_commands'])) {
                $this->info('🛠️ Çalıştırılan Uzaktan Komutlar: ' . count($result['executed_commands']));
            }
            
            return Command::SUCCESS;
        }

        $this->error('❌ ' . $result['message']);
        return Command::FAILURE;
    }
}
