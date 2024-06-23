<?php

use Illuminate\Support\Facades\Route;
use Vigilant\Sites\Http\Controllers\SiteController;
use Vigilant\Sites\Http\Livewire\SiteForm;
use Vigilant\Sites\Http\Livewire\Sites;

Route::get('sites', Sites::class)->name('sites');
Route::get('sites/create', SiteForm::class)->name('site.create');
Route::get('sites/{site}', [SiteController::class, 'view'])->name('site.view');
Route::get('sites/edit/{site}', SiteForm::class)->name('site.edit');
