<?php

use Illuminate\Support\Facades\Route;
use Vigilant\OnBoarding\Livewire\OnBoard;

Route::get('setup', OnBoard::class)->name('onboard');
