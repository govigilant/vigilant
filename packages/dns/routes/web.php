<?php

use Illuminate\Support\Facades\Route;
use Vigilant\Dns\Livewire\DnsImport;
use Vigilant\Dns\Livewire\DnsMonitorForm;
use Vigilant\Dns\Livewire\DnsMonitorHistory;
use Vigilant\Dns\Livewire\DnsMonitors;

Route::prefix('dns')
    ->middleware('can:use-dns')
    ->group(function (): void {
        Route::get('dns', DnsMonitors::class)->name('dns.index');
        Route::get('dns/{monitor}/history', DnsMonitorHistory::class)->name('dns.history');
        Route::get('dns/create', DnsMonitorForm::class)->name('dns.create');
        Route::get('dns/import', DnsImport::class)->name('dns.import');
    });
