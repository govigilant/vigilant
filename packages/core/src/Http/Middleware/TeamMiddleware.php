<?php

namespace Vigilant\Core\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Vigilant\Core\Services\TeamService;

class TeamMiddleware
{
    public function __construct(protected TeamService $teamService) {}

    /**
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $this->teamService->fromAuth();

        return $next($request);
    }
}
