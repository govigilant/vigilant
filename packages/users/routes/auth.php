<?php

use Illuminate\Support\Facades\Route;
use Vigilant\Users\Http\Controllers\SocialiteController;

Route::get('authenticate/{provider}', [SocialiteController::class, 'redirect'])->name('login.socialite');
Route::get('authenticate/callback/{provider}', [SocialiteController::class, 'callback'])->name('login.socialite.callback');
