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

// Customer Portal Routes
Route::prefix('portal')->middleware(['auth'])->group(function (): void {
    Route::get('dashboard', [\App\Http\Controllers\Api\PortalApiController::class, 'dashboard']);
    Route::get('subscription', [\App\Http\Controllers\Api\PortalApiController::class, 'subscription']);
    Route::get('extensions', [\App\Http\Controllers\Api\PortalApiController::class, 'extensions']);
    Route::get('usage', [\App\Http\Controllers\Api\PortalApiController::class, 'usage']);
    Route::get('invoices', [\App\Http\Controllers\Api\PortalApiController::class, 'invoices']);
    
    Route::post('api-keys', [\App\Http\Controllers\Api\PortalApiController::class, 'createApiKey']);
    Route::delete('api-keys/{id}', [\App\Http\Controllers\Api\PortalApiController::class, 'revokeApiKey']);
    
    Route::post('support/ticket', [\App\Http\Controllers\Api\PortalApiController::class, 'createSupportTicket']);
});

// Identity & Authentication Platform Routes
Route::prefix('identity')->group(function () {
    Route::post('/login', [\App\Http\Controllers\Api\IdentityApiController::class, 'login']);
    Route::post('/logout', [\App\Http\Controllers\Api\IdentityApiController::class, 'logout'])->middleware('auth:sanctum');
    Route::post('/mfa/enable', [\App\Http\Controllers\Api\IdentityApiController::class, 'enableMFA'])->middleware('auth:sanctum');
    Route::post('/mfa/verify', [\App\Http\Controllers\Api\IdentityApiController::class, 'verifyMFA'])->middleware('auth:sanctum');
    Route::get('/sessions', [\App\Http\Controllers\Api\IdentityApiController::class, 'sessions'])->middleware('auth:sanctum');
    Route::delete('/session/{id}', [\App\Http\Controllers\Api\IdentityApiController::class, 'revokeSession'])->middleware('auth:sanctum');
});

Route::prefix('onboarding')->group(function () {
    Route::post('/start', [\App\Http\Controllers\Api\OnboardingApiController::class, 'start']);
    Route::post('/step', [\App\Http\Controllers\Api\OnboardingApiController::class, 'step']);
    Route::post('/complete', [\App\Http\Controllers\Api\OnboardingApiController::class, 'complete']);
    Route::post('/invite', [\App\Http\Controllers\Api\OnboardingApiController::class, 'invite']);
    Route::get('/status', [\App\Http\Controllers\Api\OnboardingApiController::class, 'status']);
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
    Route::get('billing/plans', [\App\Http\Controllers\Api\HQBillingApiController::class, 'getPlans']);
    Route::post('billing/subscribe', [\App\Http\Controllers\Api\HQBillingApiController::class, 'subscribe']);
    Route::post('billing/upgrade', [\App\Http\Controllers\Api\HQBillingApiController::class, 'upgrade']);
    Route::post('billing/cancel', [\App\Http\Controllers\Api\HQBillingApiController::class, 'cancel']);
    Route::get('billing/usage', [\App\Http\Controllers\Api\HQBillingApiController::class, 'getUsage']);

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

    // Observability & Security Intelligence
    Route::post('observability/logs', [\App\Http\Controllers\Api\HQObservabilityApiController::class, 'storeLog']);
    Route::post('observability/metrics', [\App\Http\Controllers\Api\HQObservabilityApiController::class, 'storeMetric']);
    Route::post('observability/traces', [\App\Http\Controllers\Api\HQObservabilityApiController::class, 'storeTrace']);
    Route::get('observability/health', [\App\Http\Controllers\Api\HQObservabilityApiController::class, 'checkHealth']);

    // Governance, Risk & Compliance (GRC)
    Route::get('governance/policies', [\App\Http\Controllers\Api\HQ\HQGovernanceApiController::class, 'policies']);
    Route::get('governance/compliance', [\App\Http\Controllers\Api\HQ\HQGovernanceApiController::class, 'compliance']);
    Route::get('governance/risk', [\App\Http\Controllers\Api\HQ\HQGovernanceApiController::class, 'risk']);
    Route::get('governance/sla', [\App\Http\Controllers\Api\HQ\HQGovernanceApiController::class, 'sla']);
    Route::get('governance/frameworks', [\App\Http\Controllers\Api\HQ\HQGovernanceApiController::class, 'frameworks']);

    // Configuration Platform
    Route::get('configuration', [\App\Http\Controllers\Api\HQ\HQConfigurationPlatformApiController::class, 'getConfigurations']);
    Route::get('configuration/versions', [\App\Http\Controllers\Api\HQ\HQConfigurationPlatformApiController::class, 'getVersions']);
    Route::post('configuration/rollback', [\App\Http\Controllers\Api\HQ\HQConfigurationPlatformApiController::class, 'rollbackConfiguration']);
    Route::get('feature-flags', [\App\Http\Controllers\Api\HQ\HQConfigurationPlatformApiController::class, 'getFeatureFlags']);
    Route::get('secrets', [\App\Http\Controllers\Api\HQ\HQConfigurationPlatformApiController::class, 'getSecrets']);
    Route::get('environment-profiles', [\App\Http\Controllers\Api\HQ\HQConfigurationPlatformApiController::class, 'getEnvironmentProfiles']);

    // Marketplace & Extension Platform
    Route::get('marketplace/extensions', [\App\Http\Controllers\Api\HQ\HQMarketplaceApiController::class, 'extensions']);
    Route::post('marketplace/install', [\App\Http\Controllers\Api\HQ\HQMarketplaceApiController::class, 'install']);
    Route::post('marketplace/update', [\App\Http\Controllers\Api\HQ\HQMarketplaceApiController::class, 'update']);
    Route::post('marketplace/remove', [\App\Http\Controllers\Api\HQ\HQMarketplaceApiController::class, 'remove']);
});
