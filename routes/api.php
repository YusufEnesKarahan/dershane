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
    Route::post('backup/start', [\App\Http\Controllers\Api\HQBackupApiController::class, 'start']);
    Route::post('backup/report', [\App\Http\Controllers\Api\HQBackupApiController::class, 'report']);
    Route::get('backup/status', [\App\Http\Controllers\Api\HQBackupApiController::class, 'status']);
    Route::get('backup/policies', [\App\Http\Controllers\Api\HQBackupApiController::class, 'policies']);
    Route::get('storage', [\App\Http\Controllers\Api\HQBackupApiController::class, 'storage']);
    
    // Restore Management
    Route::post('restore/start', [\App\Http\Controllers\Api\HQRestoreApiController::class, 'start']);

    // Configuration
    Route::post('configuration/sync', [\App\Http\Controllers\Api\HQConfigurationApiController::class, 'sync']);
    Route::post('configuration/report', [\App\Http\Controllers\Api\HQConfigurationApiController::class, 'report']);
    Route::get('configuration/version', [\App\Http\Controllers\Api\HQConfigurationApiController::class, 'version']);
    
    // Audit Trail
    Route::post('audit/report', [\App\Http\Controllers\Api\HQAuditApiController::class, 'report']);
    
    // Alerts
    Route::post('alerts/report', [\App\Http\Controllers\Api\HQAlertApiController::class, 'report']);
    
    // Billing
    Route::get('subscription/status', [\App\Http\Controllers\Api\HQBillingApiController::class, 'subscriptionStatus']);

    // Usage & Quota
    Route::post('usage/report', [\App\Http\Controllers\Api\HQUsageApiController::class, 'report']);
    Route::get('quota', [\App\Http\Controllers\Api\HQUsageApiController::class, 'quota']);
    Route::get('usage/history', [\App\Http\Controllers\Api\HQUsageApiController::class, 'history']);

    // Workflow Engine
    Route::get('workflows', [\App\Http\Controllers\Api\HQWorkflowApiController::class, 'index']);
    Route::get('workflows/history', [\App\Http\Controllers\Api\HQWorkflowApiController::class, 'history']);
    Route::post('workflows/run', [\App\Http\Controllers\Api\HQWorkflowApiController::class, 'run']);
    Route::post('workflows/trigger', [\App\Http\Controllers\Api\HQWorkflowApiController::class, 'trigger']);
    Route::get('workflows/{workflow}', [\App\Http\Controllers\Api\HQWorkflowApiController::class, 'show']);

    // Fleet Management & Deployment
    Route::post('deployment/start', [\App\Http\Controllers\Api\HQDeploymentApiController::class, 'start']);
    Route::post('deployment/report', [\App\Http\Controllers\Api\HQDeploymentApiController::class, 'report']);
    Route::get('deployment/status', [\App\Http\Controllers\Api\HQDeploymentApiController::class, 'status']);
    Route::get('release-channel', [\App\Http\Controllers\Api\HQDeploymentApiController::class, 'releaseChannels']);

    // IAM & Auth
    Route::post('auth/api-key/create', [\App\Http\Controllers\Api\HQ\HQAuthApiController::class, 'createApiKey']);
    Route::post('auth/api-key/revoke', [\App\Http\Controllers\Api\HQ\HQAuthApiController::class, 'revokeApiKey']);
    Route::get('auth/sessions', [\App\Http\Controllers\Api\HQ\HQAuthApiController::class, 'getSessions']);
    Route::post('auth/logout', [\App\Http\Controllers\Api\HQ\HQAuthApiController::class, 'logout']);
    Route::get('auth/permissions', [\App\Http\Controllers\Api\HQ\HQAuthApiController::class, 'getPermissions']);
    Route::post('auth/service-account', [\App\Http\Controllers\Api\HQ\HQAuthApiController::class, 'createServiceAccount']);
});
