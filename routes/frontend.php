<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Frontend Routes (Kurumsal Web Sitesi - Sürüm 1)
|--------------------------------------------------------------------------
*/

Route::name('frontend.')->group(function () {
    // Ana Sayfa
    Route::get('/', function () {
        return view('welcome_placeholder');
    })->name('home');

    // Kurumsal Alt Sayfaları
    Route::prefix('kurumsal')->group(function () {
        Route::get('/hakkimizda', function () {
            return view('errors.503'); // Temporary fallback view
        })->name('about');

        Route::get('/basarilarimiz', function () {
            return view('errors.503');
        })->name('achievements');

        Route::get('/sss', function () {
            return view('errors.503');
        })->name('faq');
    });

    // Kurslar
    Route::prefix('kurslar')->name('courses.')->group(function () {
        Route::get('/', function () {
            return view('errors.503');
        })->name('index');

        Route::get('/{slug}', function (string $slug) {
            return view('errors.503');
        })->name('show');
    });

    // Branşlar
    Route::get('/branslar', function () {
        return view('errors.503');
    })->name('branches');

    // Öğretmenler
    Route::get('/ogretmenler', function () {
        return view('errors.503');
    })->name('teachers');

    // Galeri
    Route::get('/galeri', function () {
        return view('errors.503');
    })->name('gallery');

    // Blog
    Route::prefix('blog')->name('blogs.')->group(function () {
        Route::get('/', function () {
            return view('errors.503');
        })->name('index');

        Route::get('/{slug}', function (string $slug) {
            return view('errors.503');
        })->name('show');
    });

    // Etkinlikler
    Route::get('/etkinlikler', function () {
        return view('errors.503');
    })->name('events');

    // Duyurular
    Route::get('/duyurular', function () {
        return view('errors.503');
    })->name('announcements');

    // İletişim
    Route::get('/iletisim', function () {
        return view('errors.503');
    })->name('contact');

    // Online Ön Kayıt
    Route::get('/on-kayit', function () {
        return view('errors.503');
    })->name('pre-register');

    // Yasal Sayfalar
    Route::prefix('yasal')->name('legal.')->group(function () {
        Route::get('/kvkk', function () {
            return view('errors.503');
        })->name('kvkk');

        Route::get('/cerez-politikasi', function () {
            return view('errors.503');
        })->name('cerez');

        Route::get('/gizlilik-politikasi', function () {
            return view('errors.503');
        })->name('gizlilik');
    });

    // Style Guide / Demo Dashboard
    Route::get('/style-guide', function () {
        return view('frontend.style-guide');
    })->name('style-guide');
});
