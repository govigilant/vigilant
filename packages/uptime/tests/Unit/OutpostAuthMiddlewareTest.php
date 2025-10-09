<?php

namespace Vigilant\Uptime\Tests\Unit;

use Illuminate\Http\Request;
use Vigilant\Uptime\Http\Middleware\OutpostAuthMiddleware;
use Vigilant\Uptime\Tests\TestCase;

class OutpostAuthMiddlewareTest extends TestCase
{
    public function test_it_allows_request_with_valid_token(): void
    {
        config(['uptime.outpost_token' => 'valid-token']);

        $middleware = new OutpostAuthMiddleware();
        $request = Request::create('/test', 'GET');
        $request->headers->set('X-Outpost-Token', 'valid-token');

        $response = $middleware->handle($request, function ($req) {
            return response()->json(['success' => true]);
        });

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(['success' => true], $response->getData(true));
    }

    public function test_it_denies_request_with_invalid_token(): void
    {
        config(['uptime.outpost_token' => 'valid-token']);

        $middleware = new OutpostAuthMiddleware();
        $request = Request::create('/test', 'GET');
        $request->headers->set('X-Outpost-Token', 'invalid-token');

        $response = $middleware->handle($request, function ($req) {
            return response()->json(['success' => true]);
        });

        $this->assertEquals(401, $response->getStatusCode());
        $this->assertEquals(['message' => 'Unauthorized'], $response->getData(true));
    }

    public function test_it_denies_request_without_token(): void
    {
        config(['uptime.outpost_token' => 'valid-token']);

        $middleware = new OutpostAuthMiddleware();
        $request = Request::create('/test', 'GET');

        $response = $middleware->handle($request, function ($req) {
            return response()->json(['success' => true]);
        });

        $this->assertEquals(401, $response->getStatusCode());
        $this->assertEquals(['message' => 'Unauthorized'], $response->getData(true));
    }
}
