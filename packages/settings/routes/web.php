<?php

use Illuminate\Support\Facades\Route;
use Vigilant\Settings\Livewire\Settings;

Route::get('settings', Settings::class)->name('settings');
