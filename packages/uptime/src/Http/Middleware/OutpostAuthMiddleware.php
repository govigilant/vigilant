<?php

namespace Vigilant\Uptime\Http\Middleware;

use Illuminate\Http\Request;

class OutpostAuthMiddleware
{
    public function handle(Request $request, \Closure $next): mixed
    {
        $token = $request->header('X-Outpost-Token');

        if ($token !== config('uptime.outpost_token')) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        return $next($request);
    }
}
