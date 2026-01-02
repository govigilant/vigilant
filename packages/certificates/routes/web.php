<?php

use Illuminate\Support\Facades\Route;
use Vigilant\Certificates\Http\Controllers\CertificateMonitorController;
use Vigilant\Certificates\Livewire\CertificateMonitorForm;

Route::prefix('certificates')
    ->group(function () {
        Route::get('/', [CertificateMonitorController::class, 'list'])->name('certificates');
        Route::get('/create', CertificateMonitorForm::class)->name('certificates.create');
        Route::get('/{monitor}', [CertificateMonitorController::class, 'index'])->name('certificates.index')->can('view,monitor');
        Route::get('/edit/{monitor}', CertificateMonitorForm::class)->name('certificates.edit');
        Route::delete('/{monitor}', [CertificateMonitorController::class, 'delete'])->name('certificates.delete')->can('delete,monitor');
    });
