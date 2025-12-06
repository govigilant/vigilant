<?php

use Illuminate\Support\Facades\Route;
use Vigilant\Lighthouse\Http\Controllers\LighthouseMonitorController;
use Vigilant\Lighthouse\Http\Controllers\LighthouseResultController;
use Vigilant\Lighthouse\Livewire\LighthouseSiteForm;
use Vigilant\Lighthouse\Livewire\LighthouseSites;

Route::prefix('lighthouse')
    ->middleware('can:use-lighthouse')
    ->group(function (): void {
        Route::get('/', LighthouseSites::class)->name('lighthouse');
        Route::get('/create', LighthouseSiteForm::class)->name('lighthouse.create');
        Route::get('/{monitor}', [LighthouseMonitorController::class, 'index'])->name('lighthouse.index')->can('view,monitor');
        Route::delete('/{monitor}', [LighthouseMonitorController::class, 'delete'])->name('lighthouse.delete')->can('delete,monitor');
        Route::get('/{monitor}/edit', LighthouseSiteForm::class)->name('lighthouse.edit');
        Route::get('/ressult/{result}', [LighthouseResultController::class, 'index'])->name('lighthouse.result.index');
    });
