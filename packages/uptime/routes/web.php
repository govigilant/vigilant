<?php

use Illuminate\Support\Facades\Route;
use Vigilant\Uptime\Http\Controllers\UptimeMonitorController;
use Vigilant\Uptime\Http\Livewire\UptimeMonitorForm;
use Vigilant\Uptime\Http\Livewire\UptimeMonitors;

Route::prefix('uptime')
    ->middleware('can:use-uptime')
    ->group(function (): void {
        Route::get('/', UptimeMonitors::class)->name('uptime');
        Route::get('/create-monitor', UptimeMonitorForm::class)->name('uptime.monitor.create');
        Route::get('/monitor/{monitor}', [UptimeMonitorController::class, 'index'])->name('uptime.monitor.view');
        Route::get('/monitor/{monitor}/edit', UptimeMonitorForm::class)->name('uptime.monitor.edit');
    });
