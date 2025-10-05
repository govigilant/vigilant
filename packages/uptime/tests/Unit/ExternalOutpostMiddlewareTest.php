<?php

namespace Vigilant\Uptime\Tests\Unit;

use Illuminate\Http\Request;
use Vigilant\Uptime\Http\Middleware\ExternalOutpostMiddleware;
use Vigilant\Uptime\Tests\TestCase;

class ExternalOutpostMiddlewareTest extends TestCase
{
    public function test_it_allows_private_ip_when_external_outposts_disabled(): void
    {
        config(['uptime.allow_external_outposts' => false]);

        $middleware = new ExternalOutpostMiddleware();
        $request = Request::create('/test', 'GET', ['ip' => '192.168.1.1']);

        $response = $middleware->handle($request, function ($req) {
            return response()->json(['success' => true]);
        });

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(['success' => true], $response->getData(true));
    }

    public function test_it_allows_localhost_when_external_outposts_disabled(): void
    {
        config(['uptime.allow_external_outposts' => false]);

        $middleware = new ExternalOutpostMiddleware();
        $request = Request::create('/test', 'GET', ['ip' => '127.0.0.1']);

        $response = $middleware->handle($request, function ($req) {
            return response()->json(['success' => true]);
        });

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(['success' => true], $response->getData(true));
    }

    public function test_it_denies_public_ip_when_external_outposts_disabled(): void
    {
        config(['uptime.allow_external_outposts' => false]);

        $middleware = new ExternalOutpostMiddleware();
        $request = Request::create('/test', 'GET', ['ip' => '8.8.8.8']);

        $response = $middleware->handle($request, function ($req) {
            return response()->json(['success' => true]);
        });

        $this->assertEquals(403, $response->getStatusCode());
        $this->assertEquals(['message' => 'External outposts are not allowed.'], $response->getData(true));
    }

    public function test_it_allows_public_ip_when_external_outposts_enabled(): void
    {
        config(['uptime.allow_external_outposts' => true]);

        $middleware = new ExternalOutpostMiddleware();
        $request = Request::create('/test', 'GET', ['ip' => '8.8.8.8']);

        $response = $middleware->handle($request, function ($req) {
            return response()->json(['success' => true]);
        });

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(['success' => true], $response->getData(true));
    }

    public function test_it_allows_private_ip_when_external_outposts_enabled(): void
    {
        config(['uptime.allow_external_outposts' => true]);

        $middleware = new ExternalOutpostMiddleware();
        $request = Request::create('/test', 'GET', ['ip' => '10.0.0.1']);

        $response = $middleware->handle($request, function ($req) {
            return response()->json(['success' => true]);
        });

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(['success' => true], $response->getData(true));
    }

    public function test_it_allows_reserved_ip_when_external_outposts_disabled(): void
    {
        config(['uptime.allow_external_outposts' => false]);

        $middleware = new ExternalOutpostMiddleware();
        $request = Request::create('/test', 'GET', ['ip' => '169.254.1.1']);

        $response = $middleware->handle($request, function ($req) {
            return response()->json(['success' => true]);
        });

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(['success' => true], $response->getData(true));
    }

    public function test_it_allows_ipv6_private_address(): void
    {
        config(['uptime.allow_external_outposts' => false]);

        $middleware = new ExternalOutpostMiddleware();
        $request = Request::create('/test', 'GET', ['ip' => 'fd00::1']);

        $response = $middleware->handle($request, function ($req) {
            return response()->json(['success' => true]);
        });

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(['success' => true], $response->getData(true));
    }

    public function test_it_denies_ipv6_public_address_when_external_outposts_disabled(): void
    {
        config(['uptime.allow_external_outposts' => false]);

        $middleware = new ExternalOutpostMiddleware();
        $request = Request::create('/test', 'GET', ['ip' => '2001:4860:4860::8888']);

        $response = $middleware->handle($request, function ($req) {
            return response()->json(['success' => true]);
        });

        $this->assertEquals(403, $response->getStatusCode());
        $this->assertEquals(['message' => 'External outposts are not allowed.'], $response->getData(true));
    }

    public function test_it_allows_ipv6_public_address_when_external_outposts_enabled(): void
    {
        config(['uptime.allow_external_outposts' => true]);

        $middleware = new ExternalOutpostMiddleware();
        $request = Request::create('/test', 'GET', ['ip' => '2001:4860:4860::8888']);

        $response = $middleware->handle($request, function ($req) {
            return response()->json(['success' => true]);
        });

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(['success' => true], $response->getData(true));
    }
}
