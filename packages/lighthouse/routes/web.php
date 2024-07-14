<?php

use Illuminate\Support\Facades\Route;
use Vigilant\Lighthouse\Http\Controllers\LighthouseMonitorController;
use Vigilant\Lighthouse\Http\Controllers\LighthouseResultController;
use Vigilant\Lighthouse\Livewire\LighthouseSiteForm;
use Vigilant\Lighthouse\Livewire\LighthouseSites;

Route::get('lighthouse', LighthouseSites::class)->name('lighthouse');
Route::get('lighthouse/create', LighthouseSiteForm::class)->name('lighthouse.create');
Route::get('lighthouse/{monitor}', [LighthouseMonitorController::class, 'index'])->name('lighthouse.index');
Route::get('lighthouse/ressult/{result}', [LighthouseResultController::class, 'index'])->name('lighthouse.result.index');
