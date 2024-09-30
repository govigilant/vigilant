<?php

namespace Vigilant\OnBoarding\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Vigilant\OnBoarding\Actions\ShouldOnboard;

class OnlyOnboarding
{
    /**
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        /** @var ShouldOnboard $shouldOnboard */
        $shouldOnboard = app(ShouldOnboard::class);

        if (! $shouldOnboard->shouldOnboard()) {
            return redirect()->route('sites');
        }

        return $next($request);

    }
}
