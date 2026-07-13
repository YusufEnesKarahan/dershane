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
        return view('frontend.home');
    })->name('home');

    // Kurumsal Alt Sayfaları
    Route::prefix('kurumsal')->group(function () {
        Route::get('/hakkimizda', function () {
            return view('frontend.about');
        })->name('about');

        Route::get('/basarilarimiz', function () {
            return view('frontend.achievements');
        })->name('achievements');

        Route::get('/sss', function () {
            return view('frontend.faq');
        })->name('faq');
    });

    // Kurslar
    Route::prefix('kurslar')->name('courses.')->group(function () {
        Route::get('/', function () {
            return view('frontend.courses.index');
        })->name('index');

        Route::get('/{slug}', function (string $slug) {
            return view('frontend.courses.show', compact('slug'));
        })->name('show');
    });

    // Branşlar
    Route::get('/branslar', function () {
        return view('frontend.branches');
    })->name('branches');

    // Öğretmenler
    Route::get('/ogretmenler', function () {
        return view('frontend.teachers');
    })->name('teachers');

    // Galeri
    Route::get('/galeri', function () {
        return view('frontend.gallery');
    })->name('gallery');

    // Blog
    Route::prefix('blog')->name('blogs.')->group(function () {
        Route::get('/', function () {
            return view('frontend.blogs.index');
        })->name('index');

        Route::get('/{slug}', function (string $slug) {
            return view('frontend.blogs.show', compact('slug'));
        })->name('show');
    });

    // Etkinlikler
    Route::get('/etkinlikler', function () {
        return view('frontend.events');
    })->name('events');

    // Duyurular
    Route::get('/duyurular', function () {
        return view('frontend.announcements');
    })->name('announcements');

    // İletişim
    Route::get('/iletisim', function () {
        return view('frontend.contact');
    })->name('contact');

    // Online Ön Kayıt
    Route::get('/on-kayit', function () {
        return view('frontend.pre-register');
    })->name('pre-register');

    // Yasal Sayfalar
    Route::prefix('yasal')->name('legal.')->group(function () {
        Route::get('/kvkk', function () {
            return view('frontend.legal.kvkk');
        })->name('kvkk');

        Route::get('/cerez-politikasi', function () {
            return view('frontend.legal.cerez');
        })->name('cerez');

        Route::get('/gizlilik-politikasi', function () {
            return view('frontend.legal.gizlilik');
        })->name('gizlilik');
    });

    // Style Guide / Demo Dashboard
    Route::get('/style-guide', function () {
        return view('frontend.style-guide');
    })->name('style-guide');
});
