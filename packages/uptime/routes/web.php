<?php

use Illuminate\Support\Facades\Route;
use Vigilant\Uptime\Http\Livewire\CreateUptimeMonitor;
use Vigilant\Uptime\Http\Livewire\UptimeMonitors;

Route::get('uptime', UptimeMonitors::class)->name('uptime');
Route::get('uptime/create-monitor', CreateUptimeMonitor::class)->name('uptime.create');
