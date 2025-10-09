<?php

use Illuminate\Support\Facades\Route;
use Vigilant\Uptime\Http\Controllers\Api\OutpostController;
use Vigilant\Uptime\Http\Middleware\ExternalOutpostMiddleware;
use Vigilant\Uptime\Http\Middleware\OutpostAuthMiddleware;

Route::prefix('v1')->group(function (): void {
    Route::prefix('outposts')
        ->middleware([OutpostAuthMiddleware::class, ExternalOutpostMiddleware::class])
        ->group(function (): void {
            Route::post('register', [OutpostController::class, 'register']);
            Route::post('unregister', [OutpostController::class, 'unregister']);
        });
});
