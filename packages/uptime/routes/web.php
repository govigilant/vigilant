<?php

use Illuminate\Support\Facades\Route;
use Vigilant\Uptime\Http\Livewire\UptimeMonitorForm;
use Vigilant\Uptime\Http\Livewire\UptimeMonitors;

Route::get('uptime', UptimeMonitors::class)->name('uptime');
Route::get('uptime/create-monitor', UptimeMonitorForm::class)->name('uptime.monitor.create');
Route::get('uptime/monitor/{monitor}', UptimeMonitorForm::class)->name('uptime.monitor.edit');
