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


