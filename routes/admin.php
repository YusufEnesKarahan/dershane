<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Panel Routes (Kurumsal + Yönetim & Full ERP - Sürüm 2 & 3)
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->name('admin.')->group(function (): void {
    Route::get('/dashboard', function () {
        return 'Admin Dashboard Placeholder';
    })->name('dashboard');
});
