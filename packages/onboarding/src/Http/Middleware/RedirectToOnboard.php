<?php

namespace Vigilant\OnBoarding\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\Response;
use Vigilant\OnBoarding\Actions\ShouldOnboard;
use Vigilant\Users\Models\User;

class RedirectToOnboard
{
    /**
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        /** @var ShouldOnboard $shouldOnboard */
        $shouldOnboard = app(ShouldOnboard::class);

        /** @var ?User $user */
        $user = auth()->user();

        if (
            $user === null ||
            $user->email_verified_at === null ||
            Route::is('onboard*') ||
            Route::is('livewire.*') ||
            Route::is('quick-setup') ||
            ! $shouldOnboard->shouldOnboard()
        ) {
            return $next($request);
        }

        return redirect()->route('onboard');
    }
}
