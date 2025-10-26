<?php

namespace Vigilant\Uptime\Tests\Unit;

use Illuminate\Support\Facades\Http;
use Mockery\MockInterface;
use Vigilant\Dns\Actions\ResolveRecord;
use Vigilant\Uptime\Actions\FetchGeolocation;
use Vigilant\Uptime\Tests\TestCase;

class FetchGeolocationTest extends TestCase
{
    public function test_it_fetches_geolocation_for_hostname(): void
    {
        $this->mock(ResolveRecord::class, function (MockInterface $mock) {
            $mock->shouldReceive('resolve')->andReturn('93.184.216.34');
        });

        Http::fake([
            'https://free.freeipapi.com/api/json/*' => Http::response([
                'countryCode' => 'US',
                'latitude' => 40.7128,
                'longitude' => -74.0060,
            ]),
        ]);

        $fetchGeolocation = app(FetchGeolocation::class);

        $result = $fetchGeolocation->fetch('example.com');

        $this->assertNotNull($result);
        $this->assertEquals('US', $result['country']);
        $this->assertEquals(40.7128, $result['latitude']);
        $this->assertEquals(-74.0060, $result['longitude']);
    }

    public function test_it_extracts_hostname_from_url(): void
    {
        $this->mock(ResolveRecord::class, function (MockInterface $mock) {
            $mock->shouldReceive('resolve')->andReturn('93.184.216.34');
        });

        Http::fake([
            'https://free.freeipapi.com/api/json/93.184.216.34' => Http::response([
                'countryCode' => 'UK',
                'latitude' => 51.5074,
                'longitude' => -0.1278,
            ]),
        ]);

        $fetchGeolocation = app(FetchGeolocation::class);

        $result = $fetchGeolocation->fetch('https://example.com/path/to/resource');

        $this->assertNotNull($result);
        $this->assertEquals('UK', $result['country']);
    }

    public function test_it_extracts_hostname_from_host_port_format(): void
    {
        Http::fake([
            'https://free.freeipapi.com/api/json/192.168.1.1' => Http::response([
                'countryCode' => 'DE',
                'latitude' => 52.5200,
                'longitude' => 13.4050,
            ]),
        ]);

        $fetchGeolocation = app(FetchGeolocation::class);

        $result = $fetchGeolocation->fetch('192.168.1.1:8080');

        $this->assertNotNull($result);
        $this->assertEquals('DE', $result['country']);
    }

    public function test_it_returns_null_on_api_failure(): void
    {
        $this->mock(ResolveRecord::class, function (MockInterface $mock) {
            $mock->shouldReceive('resolve')->andReturn('93.184.216.34');
        });

        Http::fake([
            'https://free.freeipapi.com/api/json/*' => Http::response([], 500),
        ]);

        $fetchGeolocation = app(FetchGeolocation::class);

        $result = $fetchGeolocation->fetch('example.com');

        $this->assertNull($result);
    }

    public function test_it_returns_null_on_exception(): void
    {
        $this->mock(ResolveRecord::class, function (MockInterface $mock) {
            $mock->shouldReceive('resolve')->andReturn('93.184.216.34');
        });

        Http::fake([
            'https://free.freeipapi.com/api/json/*' => function () {
                throw new \Exception('Network error');
            },
        ]);

        $fetchGeolocation = app(FetchGeolocation::class);

        $result = $fetchGeolocation->fetch('example.com');

        $this->assertNull($result);
    }
}
