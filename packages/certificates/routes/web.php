<?php

use Illuminate\Support\Facades\Route;

Route::prefix('certificates')
    ->group(function () {
        Route::view('/', 'certificates::index')->name('certificates');
        Route::view('/create', 'certificates::index')->name('certificates.create');
        Route::view('/{monitor}', 'certificates::index')->name('certificates.index');
    });
