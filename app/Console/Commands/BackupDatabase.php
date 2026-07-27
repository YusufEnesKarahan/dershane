<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class BackupDatabase extends Command
{
    protected $signature = 'backup:database';
    protected $description = 'Creates a database backup file and cleans up backups older than 7 days.';

    public function handle(): int
    {
        $this->info('Starting database backup process...');

        $backupDir = storage_path('app/backups');
        if (!file_exists($backupDir)) {
            mkdir($backupDir, 0755, true);
        }

        $filename = 'backup-' . date('Y-m-d_H-i-s') . '.sql';
        $filepath = $backupDir . '/' . $filename;

        $connection = config('database.default');

        try {
            if ($connection === 'sqlite') {
                $dbPath = config('database.connections.sqlite.database');
                if (file_exists($dbPath)) {
                    copy($dbPath, $filepath);
                }
            } else {
                $host = config('database.connections.mysql.host');
                $user = config('database.connections.mysql.username');
                $pass = config('database.connections.mysql.password');
                $name = config('database.connections.mysql.database');

                $command = sprintf(
                    'mysqldump --user="%s" --password="%s" --host="%s" "%s" > "%s"',
                    $user,
                    $pass,
                    $host,
                    $name,
                    $filepath
                );

                exec($command);
            }

            $this->info("Backup successfully created at: {$filepath}");
            Log::info("Database backup created successfully: {$filename}");

            // Clean up backups older than 7 days
            $files = glob($backupDir . '/*');
            $now = time();

            foreach ($files as $file) {
                if (is_file($file) && ($now - filemtime($file)) > (7 * 86400)) {
                    unlink($file);
                    $this->info("Cleaned up old backup: " . basename($file));
                }
            }

            return Command::SUCCESS;
        } catch (\Throwable $e) {
            $this->error('Backup failed: ' . $e->getMessage());
            Log::error('Database backup failed: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
