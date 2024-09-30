<?php

namespace Vigilant\OnBoarding\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\Response;
use Vigilant\OnBoarding\Actions\ShouldOnboard;

class RedirectToOnboard
{
    const SESSION_KEY = 'onboarding_redirect';

    /**
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        /** @var ShouldOnboard $shouldOnboard */
        $shouldOnboard = app(ShouldOnboard::class);

        if (
            session()->has(static::SESSION_KEY) ||
            auth()->user() === null ||
            Route::is('onboard') ||
            ! $shouldOnboard->shouldOnboard()
        ) {
            return $next($request);
        }

        session()->put(static::SESSION_KEY, true);

        return redirect()->route('onboard');
    }
}
