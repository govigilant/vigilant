<?php

use Illuminate\Support\Facades\Route;
use Vigilant\Lighthouse\Http\Controllers\LighthouseCallbackController;

Route::post('/callback/{monitorId}/{batch}/{worker}', [LighthouseCallbackController::class, 'result'])
    ->middleware('signed:relative')
    ->name('lighthouse.callback');
