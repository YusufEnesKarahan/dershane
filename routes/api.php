<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function (): void {
    // API routes placeholder
});

// HQ Central Management Backend Routes
Route::prefix('hq')->middleware([\App\Http\Middleware\VerifyHQApiSignature::class])->group(function (): void {
    Route::post('register', [\App\Http\Controllers\Api\HQCentralApiController::class, 'register']);
    Route::post('heartbeat', [\App\Http\Controllers\Api\HQCentralApiController::class, 'heartbeat']);
    Route::post('telemetry', [\App\Http\Controllers\Api\HQCentralApiController::class, 'telemetry']);
    Route::get('commands', [\App\Http\Controllers\Api\HQCentralApiController::class, 'commands']);
    Route::post('commands/{id}/result', [\App\Http\Controllers\Api\HQCentralApiController::class, 'commandResult']);
    Route::post('license/validate', [\App\Http\Controllers\Api\HQLicenseValidationController::class, 'validate']);
    
    // Updates
    Route::post('update/check', [\App\Http\Controllers\Api\HQUpdateApiController::class, 'checkUpdate']);
    Route::post('update/start', [\App\Http\Controllers\Api\HQUpdateApiController::class, 'startUpdate']);
    Route::post('update/progress', [\App\Http\Controllers\Api\HQUpdateApiController::class, 'reportProgress']);
    Route::post('update/finished', [\App\Http\Controllers\Api\HQUpdateApiController::class, 'reportFinished']);

    // Backup Management
    Route::post('backup/check', [\App\Http\Controllers\Api\HQBackupApiController::class, 'check']);
    Route::post('backup/start', [\App\Http\Controllers\Api\HQBackupApiController::class, 'start']);
    Route::post('backup/progress', [\App\Http\Controllers\Api\HQBackupApiController::class, 'progress']);
    Route::post('backup/finished', [\App\Http\Controllers\Api\HQBackupApiController::class, 'finished']);

    // Configuration
    Route::post('configuration/sync', [\App\Http\Controllers\Api\HQConfigurationApiController::class, 'sync']);
    Route::post('configuration/report', [\App\Http\Controllers\Api\HQConfigurationApiController::class, 'report']);
    Route::get('configuration/version', [\App\Http\Controllers\Api\HQConfigurationApiController::class, 'version']);
    
    // Audit Trail
    Route::post('audit/report', [\App\Http\Controllers\Api\HQAuditApiController::class, 'report']);
});
