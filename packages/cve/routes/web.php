<?php

use Illuminate\Support\Facades\Route;
use Vigilant\Cve\Livewire\CveMonitorForm;

Route::prefix('cve')
    ->group(function () {
        Route::view('/', 'cve::index')->name('cve.index');
        Route::get('/create', CveMonitorForm::class)->name('cve.create');
        Route::get('/edit/{monitor}', CveMonitorForm::class)->name('cve.edit');
    });
