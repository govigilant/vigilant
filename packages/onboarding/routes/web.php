<?php

use Illuminate\Support\Facades\Route;
use Vigilant\OnBoarding\Http\Middleware\OnlyOnboarding;
use Vigilant\OnBoarding\Livewire\OnBoard;

Route::get('setup', OnBoard::class)
    ->middleware(OnlyOnboarding::class)
    ->name('onboard');
