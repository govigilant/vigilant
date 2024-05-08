<?php

use Illuminate\Support\Facades\Route;
use Vigilant\Uptime\Http\Controllers\UptimeMonitorController;
use Vigilant\Uptime\Http\Livewire\UptimeMonitorForm;
use Vigilant\Uptime\Http\Livewire\UptimeMonitors;

Route::get('uptime', UptimeMonitors::class)->name('uptime');
Route::get('uptime/create-monitor', UptimeMonitorForm::class)->name('uptime.monitor.create');
Route::get('uptime/monitor/{monitor}', [UptimeMonitorController::class, 'index'])->name('uptime.monitor.view');
Route::get('uptime/monitor/{monitor}/edit', UptimeMonitorForm::class)->name('uptime.monitor.edit');
