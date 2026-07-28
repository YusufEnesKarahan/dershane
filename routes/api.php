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
});
