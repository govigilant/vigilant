<?php

use Illuminate\Support\Facades\Route;
use Vigilant\Healthchecks\Http\Controllers\HealthcheckController;
use Vigilant\Healthchecks\Livewire\HealthcheckForm;
use Vigilant\Healthchecks\Livewire\Healthchecks;
use Vigilant\Healthchecks\Livewire\HealthcheckSetup;

Route::prefix('healthchecks')
    ->middleware('can:use-healthchecks')
    ->group(function (): void {
        Route::get('/', Healthchecks::class)->name('healthchecks.index');
        Route::get('/create', HealthcheckForm::class)->name('healthchecks.create');
        Route::get('/{healthcheck}', [HealthcheckController::class, 'index'])->name('healthchecks.view');
        Route::get('/{healthcheck}/setup', HealthcheckSetup::class)->name('healthchecks.setup');
        Route::delete('/{healthcheck}', [HealthcheckController::class, 'delete'])->name('healthchecks.delete')->can('delete,healthcheck');
        Route::get('/{healthcheck}/edit', HealthcheckForm::class)->name('healthchecks.edit');
    });
