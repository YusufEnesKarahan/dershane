<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Frontend Routes (Kurumsal Web Sitesi - Sürüm 1)
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome_placeholder'); // Simple root fallback or kurumsal site view
})->name('frontend.home');
