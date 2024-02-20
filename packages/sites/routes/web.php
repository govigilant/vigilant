<?php

use Illuminate\Support\Facades\Route;
use Vigilant\Sites\Http\Livewire\Create;
use Vigilant\Sites\Http\Livewire\Sites;

Route::get('sites', Sites::class)->name('sites');
Route::get('site/create', Create::class)->name('site.create');
