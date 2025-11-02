<?php

use Illuminate\Support\Facades\Route;
use Vigilant\Sites\Http\Controllers\SiteController;
use Vigilant\Sites\Http\Livewire\ImportSites;
use Vigilant\Sites\Http\Livewire\SiteForm;
use Vigilant\Sites\Http\Livewire\Sites;

Route::get('site', Sites::class)->name('sites');
Route::get('site/create', SiteForm::class)->name('site.create');
Route::get('site/{site}', [SiteController::class, 'view'])->name('site.view');
Route::get('site/edit/{site}', SiteForm::class)->name('site.edit');
Route::delete('site/{site}', [SiteController::class, 'delete'])->name('site.delete');
Route::get('sites/import', ImportSites::class)->name('site.import');
