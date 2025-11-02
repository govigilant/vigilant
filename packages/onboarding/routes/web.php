<?php

use Illuminate\Support\Facades\Route;
use Vigilant\OnBoarding\Http\Middleware\OnlyOnboarding;
use Vigilant\OnBoarding\Livewire\Complete;
use Vigilant\OnBoarding\Livewire\ImportDomains;
use Vigilant\OnBoarding\Livewire\NotificationChannel;

Route::middleware(OnlyOnboarding::class)->group(function () {
    Route::get('setup', ImportDomains::class)
        ->name('onboard');

    Route::get('setup/notifications', NotificationChannel::class)
        ->name('onboard.notifications');

    Route::get('setup/complete', Complete::class)
        ->name('onboard.complete');
});
