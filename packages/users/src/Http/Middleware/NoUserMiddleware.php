<?php

namespace Vigilant\Users\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Vigilant\Users\Models\User;

class NoUserMiddleware
{
    public function handle(Request $request, Closure $next): mixed
    {
        if ($request->routeIs('register') || auth()->check()) {
            return $next($request);
        }

        $userCount = User::query()->count();

        if ($userCount === 0) {
           return redirect()->route('register');
        }

        return $next($request);
    }
}
