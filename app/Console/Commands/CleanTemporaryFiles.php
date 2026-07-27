<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CleanTemporaryFiles extends Command
{
    protected $signature = 'storage:clean-temp';
    protected $description = 'Cleans up temporary export files, old logs, and cached upload fragments.';

    public function handle(): int
    {
        $this->info('Starting temporary storage cleanup...');

        $directories = [
            storage_path('app/exports'),
            storage_path('app/temp'),
            storage_path('framework/views'),
        ];

        $cleanedCount = 0;
        $now = time();

        foreach ($directories as $dir) {
            if (!file_exists($dir)) {
                continue;
            }

            $files = glob($dir . '/*');
            foreach ($files as $file) {
                // Delete files older than 24 hours
                if (is_file($file) && ($now - filemtime($file)) > 86400) {
                    @unlink($file);
                    $cleanedCount++;
                }
            }
        }

        $this->info("Cleaned up {$cleanedCount} temporary files.");
        Log::info("Storage cleanup completed: {$cleanedCount} temporary files removed.");

        return Command::SUCCESS;
    }
}
