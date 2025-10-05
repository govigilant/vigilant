<?php

namespace Vigilant\Uptime\Http\Middleware;

use Illuminate\Http\Request;

class ExternalOutpostMiddleware
{
    public function handle(Request $request, \Closure $next): mixed
    {
        $allowExternalOutposts = config('uptime.allow_external_outposts', false);

        $ip = $request->ip();

        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
            if (! $allowExternalOutposts) {
                return response()->json(['message' => 'External outposts are not allowed.'], 400);
            }
        }

        return $next($request);
    }
}
