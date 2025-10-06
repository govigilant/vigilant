<?php

namespace Vigilant\Uptime\Tests\Unit;

use Vigilant\Uptime\Actions\Outpost\DetermineOutpost;
use Vigilant\Uptime\Enums\OutpostStatus;
use Vigilant\Uptime\Enums\Type;
use Vigilant\Uptime\Models\Monitor;
use Vigilant\Uptime\Models\Outpost;
use Vigilant\Uptime\Tests\TestCase;

class DetermineOutpostTest extends TestCase
{
    public function test_it_returns_null_when_no_outposts_available(): void
    {
        $determineOutpost = new DetermineOutpost;

        $monitor = Monitor::query()->create([
            'team_id' => 1,
            'name' => 'Test Monitor',
            'type' => Type::Http,
            'settings' => ['host' => 'http://example.com'],
            'interval' => '* * * * *',
            'retries' => 1,
            'timeout' => 5,
        ]);

        $result = $determineOutpost->determine($monitor);

        $this->assertNull($result);
    }

    public function test_it_selects_same_country_outpost_approximately_50_percent(): void
    {
        // Create a monitor in the US
        $monitor = Monitor::query()->create([
            'team_id' => 1,
            'name' => 'Test Monitor',
            'type' => Type::Http,
            'settings' => ['host' => 'http://example.com'],
            'interval' => '* * * * *',
            'retries' => 1,
            'timeout' => 5,
            'country' => 'US',
        ]);

        // Create outposts: two in US, one in UK, one in DE
        $usOutpost1 = Outpost::query()->create([
            'ip' => '192.168.1.1',
            'port' => 8080,
            'external_ip' => '1.2.3.4',
            'status' => OutpostStatus::Available,
            'country' => 'US',
            'latitude' => 42.3601,
            'longitude' => -71.0589,
            'last_available_at' => now(),
        ]);

        $usOutpost2 = Outpost::query()->create([
            'ip' => '192.168.1.2',
            'port' => 8080,
            'external_ip' => '1.2.3.5',
            'status' => OutpostStatus::Available,
            'country' => 'US',
            'latitude' => 41.8781,
            'longitude' => -87.6298,
            'last_available_at' => now(),
        ]);

        $ukOutpost = Outpost::query()->create([
            'ip' => '192.168.1.3',
            'port' => 8080,
            'external_ip' => '1.2.3.6',
            'status' => OutpostStatus::Available,
            'country' => 'UK',
            'latitude' => 51.5074,
            'longitude' => -0.1278,
            'last_available_at' => now(),
        ]);

        $deOutpost = Outpost::query()->create([
            'ip' => '192.168.1.4',
            'port' => 8081,
            'external_ip' => '1.2.3.7',
            'status' => OutpostStatus::Available,
            'country' => 'DE',
            'latitude' => 52.5200,
            'longitude' => 13.4050,
            'last_available_at' => now(),
        ]);

        $determineOutpost = new DetermineOutpost;

        $usCount = 0;
        $remoteCount = 0;

        // Run the selection 200 times for better statistical distribution
        for ($i = 0; $i < 200; $i++) {
            $selected = $determineOutpost->determine($monitor);

            if ($selected->country === 'US') {
                $usCount++;
            } else {
                $remoteCount++;
            }
        }

        // US outposts should be selected approximately 50% of the time (allow variance for randomness)
        $this->assertGreaterThan(60, $usCount);
        $this->assertLessThan(140, $usCount);

        // Remote outposts should be selected approximately 50% of the time
        $this->assertGreaterThan(60, $remoteCount);
        $this->assertLessThan(140, $remoteCount);
    }

    public function test_it_distributes_remote_country_outposts_evenly(): void
    {
        // Create monitors with different IDs to test distribution
        $monitors = [];
        for ($i = 0; $i < 10; $i++) {
            $monitors[] = Monitor::query()->create([
                'team_id' => 1,
                'name' => "Test Monitor {$i}",
                'type' => Type::Http,
                'settings' => ['host' => 'http://example.com'],
                'interval' => '* * * * *',
                'retries' => 1,
                'timeout' => 5,
                'country' => 'US',
            ]);
        }

        // Create outposts
        $usOutpost = Outpost::query()->create([
            'ip' => '192.168.1.1',
            'port' => 8080,
            'external_ip' => '1.2.3.4',
            'status' => OutpostStatus::Available,
            'country' => 'US',
            'latitude' => 42.3601,
            'longitude' => -71.0589,
            'last_available_at' => now(),
        ]);

        $ukOutpost = Outpost::query()->create([
            'ip' => '192.168.1.2',
            'port' => 8080,
            'external_ip' => '1.2.3.5',
            'status' => OutpostStatus::Available,
            'country' => 'UK',
            'latitude' => 41.8781,
            'longitude' => -87.6298,
            'last_available_at' => now(),
        ]);

        $deOutpost = Outpost::query()->create([
            'ip' => '192.168.1.3',
            'port' => 8080,
            'external_ip' => '1.2.3.6',
            'status' => OutpostStatus::Available,
            'country' => 'DE',
            'latitude' => 51.5074,
            'longitude' => -0.1278,
            'last_available_at' => now(),
        ]);

        $determineOutpost = new DetermineOutpost;

        $ukSelections = 0;
        $deSelections = 0;

        // Test remote selection for multiple runs per monitor
        foreach ($monitors as $monitor) {
            // Run selection multiple times, filter for remote selections only
            for ($i = 0; $i < 20; $i++) {
                $selected = $determineOutpost->determine($monitor);

                if ($selected->country !== 'US') {
                    if ($selected->country === 'UK') {
                        $ukSelections++;
                    } elseif ($selected->country === 'DE') {
                        $deSelections++;
                    }
                }
            }
        }

        // Both remote outposts should have been selected
        $this->assertGreaterThan(0, $ukSelections);
        $this->assertGreaterThan(0, $deSelections);
    }

    public function test_it_handles_monitor_without_country(): void
    {
        $monitor = Monitor::query()->create([
            'team_id' => 1,
            'name' => 'Test Monitor',
            'type' => Type::Http,
            'settings' => ['host' => 'http://example.com'],
            'interval' => '* * * * *',
            'retries' => 1,
            'timeout' => 5,
        ]);

        $outpost = Outpost::query()->create([
            'ip' => '192.168.1.1',
            'port' => 8080,
            'external_ip' => '1.2.3.4',
            'status' => OutpostStatus::Available,
            'country' => 'US',
            'latitude' => 42.3601,
            'longitude' => -71.0589,
            'last_available_at' => now(),
        ]);

        $determineOutpost = new DetermineOutpost;

        $result = $determineOutpost->determine($monitor);

        $this->assertNotNull($result);
        $this->assertEquals($outpost->id, $result->id);
    }

    public function test_it_handles_single_outpost_in_same_country(): void
    {
        $monitor = Monitor::query()->create([
            'team_id' => 1,
            'name' => 'Test Monitor',
            'type' => Type::Http,
            'settings' => ['host' => 'http://example.com'],
            'interval' => '* * * * *',
            'retries' => 1,
            'timeout' => 5,
            'country' => 'US',
        ]);

        $outpost = Outpost::query()->create([
            'ip' => '192.168.1.1',
            'port' => 8080,
            'external_ip' => '1.2.3.4',
            'status' => OutpostStatus::Available,
            'country' => 'US',
            'latitude' => 42.3601,
            'longitude' => -71.0589,
            'last_available_at' => now(),
        ]);

        $determineOutpost = new DetermineOutpost;

        // Should always return the single outpost
        for ($i = 0; $i < 10; $i++) {
            $result = $determineOutpost->determine($monitor);
            $this->assertEquals($outpost->id, $result->id);
        }
    }

    public function test_it_handles_no_same_country_outposts(): void
    {
        $monitor = Monitor::query()->create([
            'team_id' => 1,
            'name' => 'Test Monitor',
            'type' => Type::Http,
            'settings' => ['host' => 'http://example.com'],
            'interval' => '* * * * *',
            'retries' => 1,
            'timeout' => 5,
            'country' => 'US',
        ]);

        // Only create outposts in other countries
        $ukOutpost = Outpost::query()->create([
            'ip' => '192.168.1.1',
            'port' => 8080,
            'external_ip' => '1.2.3.4',
            'status' => OutpostStatus::Available,
            'country' => 'UK',
            'latitude' => 51.5074,
            'longitude' => -0.1278,
            'last_available_at' => now(),
        ]);

        $deOutpost = Outpost::query()->create([
            'ip' => '192.168.1.2',
            'port' => 8080,
            'external_ip' => '1.2.3.5',
            'status' => OutpostStatus::Available,
            'country' => 'DE',
            'latitude' => 52.5200,
            'longitude' => 13.4050,
            'last_available_at' => now(),
        ]);

        $determineOutpost = new DetermineOutpost;

        // Should still return an outpost (from other countries)
        $result = $determineOutpost->determine($monitor);

        $this->assertNotNull($result);
        $this->assertContains($result->country, ['UK', 'DE']);
    }
}
