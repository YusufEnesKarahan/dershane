<?php

namespace App\Domain\System\Commands;

use App\Domain\HQ\Enums\HQCommandType;
use App\Domain\System\Commands\Handlers\PingHandler;
use App\Domain\System\Commands\Handlers\SyncLicenseHandler;
use App\Domain\System\Commands\Handlers\ClearCacheHandler;
use App\Domain\System\Commands\Handlers\TelemetryHandler;
// Use other handlers as they are created

class CommandRegistry
{
    /**
     * Map of command types to handler classes.
     * Prevents dynamic class loading and enforces whitelist.
     */
    protected static array $handlers = [
        'ping' => \App\Domain\System\Commands\Handlers\PingHandler::class,
        'clear_cache' => \App\Domain\System\Commands\Handlers\ClearCacheHandler::class,
        'sync_license' => \App\Domain\System\Commands\Handlers\SyncLicenseHandler::class,
        'upload_telemetry_now' => \App\Domain\System\Commands\Handlers\TelemetryHandler::class,
        'check_update' => \App\Domain\System\Commands\Handlers\CheckUpdateHandler::class,
        'start_update' => \App\Domain\System\Commands\Handlers\StartUpdateHandler::class,
        'report_update_progress' => \App\Domain\System\Commands\Handlers\ReportUpdateProgressHandler::class,
        'report_update_finished' => \App\Domain\System\Commands\Handlers\ReportUpdateFinishedHandler::class,
        'sync_configuration' => \App\Domain\System\Commands\Handlers\SyncConfigurationHandler::class,
        'clear_configuration_cache' => \App\Domain\System\Commands\Handlers\ClearConfigurationCacheHandler::class,
        'backup_check' => \App\Domain\System\Commands\Handlers\BackupCheckHandler::class,
        'backup_start' => \App\Domain\System\Commands\Handlers\BackupStartHandler::class,
        'backup_progress' => \App\Domain\System\Commands\Handlers\BackupProgressHandler::class,
        'backup_finished' => \App\Domain\System\Commands\Handlers\BackupFinishedHandler::class,
        'backup_restore' => \App\Domain\System\Commands\Handlers\BackupRestoreHandler::class,
    ];

    /**
     * Resolve a handler for a given command type.
     */
    public static function resolve(string $type): ?RemoteCommandHandlerInterface
    {
        // Must be a valid enum
        if (!HQCommandType::tryFrom($type)) {
            return null;
        }

        $handlerClass = self::$handlers[$type] ?? null;

        if ($handlerClass && class_exists($handlerClass)) {
            return app($handlerClass);
        }

        return null;
    }
}
