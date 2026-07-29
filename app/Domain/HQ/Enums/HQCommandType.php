<?php

namespace App\Domain\HQ\Enums;

enum HQCommandType: string
{
    case SYNC_LICENSE = 'sync_license';
    case SYNC_SETTINGS = 'sync_settings';
    case CLEAR_CACHE = 'clear_cache';
    case PING = 'ping';
    case REFRESH_FEATURES = 'refresh_features';
    case PULL_CONFIGURATION = 'pull_configuration';
    case UPLOAD_TELEMETRY_NOW = 'upload_telemetry_now';
    case SEND_HEALTH_REPORT = 'send_health_report';
    case RELOAD_LOCAL_CONFIGURATION = 'reload_local_configuration';
    case FORCE_HEARTBEAT = 'force_heartbeat';

    // Update Management
    case CHECK_UPDATE = 'check_update';
    case START_UPDATE = 'start_update';
    case REPORT_UPDATE_PROGRESS = 'report_update_progress';
    case REPORT_UPDATE_FINISHED = 'report_update_finished';
    // Configuration Management
    case SYNC_CONFIGURATION = 'sync_configuration';
    case CLEAR_CONFIGURATION_CACHE = 'clear_configuration_cache';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
