<?php

use Illuminate\Support\Facades\Route;
use Vigilant\Cve\Http\Controllers\CveController;
use Vigilant\Cve\Http\Controllers\CveMonitorController;
use Vigilant\Cve\Livewire\CveMonitorForm;

Route::prefix('cve')
    ->group(function () {
        Route::view('/', 'cve::index')->name('cve.index');
        Route::get('/create', CveMonitorForm::class)->name('cve.monitor.create');
        Route::get('/edit/{monitor}', CveMonitorForm::class)->name('cve.monitor.edit');
        Route::get('/{monitor}', [CveMonitorController::class, 'view'])->name('cve.monitor.view');
        Route::delete('/monitor', [CveMonitorController::class, 'delete'])->name('cve.monitor.delete')->can('delete,monitor');

        Route::get('/{monitor}/{cve}', [CveController::class, 'view'])->name('cve.view');
    });
