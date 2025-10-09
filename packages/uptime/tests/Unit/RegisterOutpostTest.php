<?php

namespace Vigilant\Uptime\Tests\Unit;

use Illuminate\Support\Facades\Http;
use Vigilant\Uptime\Actions\Outpost\RegisterOutpost;
use Vigilant\Uptime\Enums\OutpostStatus;
use Vigilant\Uptime\Models\Outpost;
use Vigilant\Uptime\Tests\TestCase;

class RegisterOutpostTest extends TestCase
{
    public function test_it_registers_new_outpost_with_geolocation(): void
    {
        Http::fake([
            'https://free.freeipapi.com/api/json/*' => Http::response([
                'countryCode' => 'US',
                'latitude' => 40.7128,
                'longitude' => -74.0060,
            ]),
        ]);

        $registerOutpost = app(RegisterOutpost::class);

        $outpost = $registerOutpost->register('1.2.3.4', '192.168.1.1', 8080);

        $this->assertInstanceOf(Outpost::class, $outpost);
        $this->assertEquals('1.2.3.4', $outpost->external_ip);
        $this->assertEquals('192.168.1.1', $outpost->ip);
        $this->assertEquals(8080, $outpost->port);
        $this->assertEquals('US', $outpost->country);
        $this->assertEquals(40.7128, $outpost->latitude);
        $this->assertEquals(-74.0060, $outpost->longitude);
        $this->assertEquals(OutpostStatus::Available, $outpost->status);
    }

    public function test_it_updates_existing_outpost_with_country(): void
    {
        $existingOutpost = Outpost::query()->create([
            'ip' => '192.168.1.1',
            'port' => 8080,
            'external_ip' => '1.2.3.4',
            'status' => OutpostStatus::Unavailable,
            'country' => 'US',
            'latitude' => 40.7128,
            'longitude' => -74.0060,
            'last_available_at' => now()->subHour(),
        ]);

        Http::fake();

        $registerOutpost = app(RegisterOutpost::class);

        $outpost = $registerOutpost->register('1.2.3.5', '192.168.1.1', 8080);

        $this->assertEquals($existingOutpost->id, $outpost->id);
        $this->assertEquals('1.2.3.5', $outpost->external_ip);
        $this->assertEquals(OutpostStatus::Available, $outpost->status);
        $this->assertEquals('US', $outpost->country);

        // Should not have made an HTTP request since country already exists
        Http::assertNothingSent();
    }

    public function test_it_fetches_geolocation_for_existing_outpost_without_country(): void
    {
        $existingOutpost = Outpost::query()->create([
            'ip' => '192.168.1.1',
            'port' => 8080,
            'external_ip' => '1.2.3.4',
            'status' => OutpostStatus::Unavailable,
            'country' => null,
            'last_available_at' => now()->subHour(),
        ]);

        Http::fake([
            'https://free.freeipapi.com/api/json/*' => Http::response([
                'countryCode' => 'UK',
                'latitude' => 51.5074,
                'longitude' => -0.1278,
            ]),
        ]);

        $registerOutpost = app(RegisterOutpost::class);

        $outpost = $registerOutpost->register('1.2.3.5', '192.168.1.1', 8080);

        $this->assertEquals($existingOutpost->id, $outpost->id);
        $this->assertEquals('UK', $outpost->country);
        $this->assertEquals(51.5074, $outpost->latitude);
        $this->assertEquals(-0.1278, $outpost->longitude);

        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'free.freeipapi.com');
        });
    }

    public function test_it_handles_geolocation_fetch_failure_gracefully(): void
    {
        Http::fake([
            'https://free.freeipapi.com/api/json/*' => Http::response([], 500),
        ]);

        $registerOutpost = app(RegisterOutpost::class);

        $outpost = $registerOutpost->register('1.2.3.4', '192.168.1.1', 8080);

        $this->assertInstanceOf(Outpost::class, $outpost);
        $this->assertEquals('1.2.3.4', $outpost->external_ip);
        $this->assertNull($outpost->country);
        $this->assertNull($outpost->latitude);
        $this->assertNull($outpost->longitude);
        $this->assertEquals(OutpostStatus::Available, $outpost->status);
    }
}
