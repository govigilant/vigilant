<?php

namespace Vigilant\Users\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Vigilant\Users\Models\User;

class NoUserMiddleware
{
    public function handle(Request $request, Closure $next): mixed
    {
        $isRegisterRoute = $request->routeIs('register')
            || ($request->isMethod('POST') && str_ends_with($request->url(), 'register'));

        if ($isRegisterRoute || auth()->check()) {
            return $next($request);
        }

        $userCount = User::query()->count();

        if ($userCount === 0) {
            return redirect()->route('register');
        }

        return $next($request);
    }
}
