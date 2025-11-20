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
        // Create a monitor in Boston, US
        $monitor = Monitor::query()->create([
            'team_id' => 1,
            'name' => 'Test Monitor',
            'type' => Type::Http,
            'settings' => ['host' => 'http://example.com'],
            'interval' => '* * * * *',
            'retries' => 1,
            'timeout' => 5,
            'country' => 'US',
            'latitude' => 42.3601,
            'longitude' => -71.0589,
        ]);

        // Create outposts: two in US (Boston and Chicago), one in UK (London), one in DE (Berlin)
        $usOutpost1 = Outpost::query()->create([
            'ip' => '192.168.1.1',
            'port' => 8080,
            'external_ip' => '1.2.3.4',
            'status' => OutpostStatus::Available,
            'country' => 'US',
            'latitude' => 42.3601,
            'longitude' => -71.0589, // Boston (same as monitor - closest)
            'last_available_at' => now(),
        ]);

        $usOutpost2 = Outpost::query()->create([
            'ip' => '192.168.1.2',
            'port' => 8080,
            'external_ip' => '1.2.3.5',
            'status' => OutpostStatus::Available,
            'country' => 'US',
            'latitude' => 41.8781,
            'longitude' => -87.6298, // Chicago
            'last_available_at' => now(),
        ]);

        $ukOutpost = Outpost::query()->create([
            'ip' => '192.168.1.3',
            'port' => 8080,
            'external_ip' => '1.2.3.6',
            'status' => OutpostStatus::Available,
            'country' => 'UK',
            'latitude' => 51.5074,
            'longitude' => -0.1278, // London
            'last_available_at' => now(),
        ]);

        $deOutpost = Outpost::query()->create([
            'ip' => '192.168.1.4',
            'port' => 8081,
            'external_ip' => '1.2.3.7',
            'status' => OutpostStatus::Available,
            'country' => 'DE',
            'latitude' => 52.5200,
            'longitude' => 13.4050, // Berlin
            'last_available_at' => now(),
        ]);

        $determineOutpost = new DetermineOutpost;

        $closestCount = 0;
        $remoteCount = 0;

        // Run the selection 200 times for better statistical distribution
        for ($i = 0; $i < 200; $i++) {
            $selected = $determineOutpost->determine($monitor);
            $this->assertNotNull($selected);

            if ($selected->id === $usOutpost1->id) {
                $closestCount++;
            } else {
                $remoteCount++;
            }
        }

        // Closest outpost (Boston) should be selected approximately 50% of the time (allow variance for randomness)
        $this->assertGreaterThan(60, $closestCount);
        $this->assertLessThan(140, $closestCount);

        // Remote outposts should be selected approximately 50% of the time
        $this->assertGreaterThan(60, $remoteCount);
        $this->assertLessThan(140, $remoteCount);
    }

    public function test_it_distributes_remote_country_outposts_evenly(): void
    {
        // Create monitors with different IDs and locations to test distribution
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
                'latitude' => 42.3601,
                'longitude' => -71.0589, // Boston
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
            'longitude' => -71.0589, // Boston (closest)
            'last_available_at' => now(),
        ]);

        $ukOutpost = Outpost::query()->create([
            'ip' => '192.168.1.2',
            'port' => 8080,
            'external_ip' => '1.2.3.5',
            'status' => OutpostStatus::Available,
            'country' => 'UK',
            'latitude' => 51.5074,
            'longitude' => -0.1278, // London (farthest from Boston)
            'last_available_at' => now(),
        ]);

        $deOutpost = Outpost::query()->create([
            'ip' => '192.168.1.3',
            'port' => 8080,
            'external_ip' => '1.2.3.6',
            'status' => OutpostStatus::Available,
            'country' => 'DE',
            'latitude' => 52.5200,
            'longitude' => 13.4050, // Berlin
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
                $this->assertNotNull($selected);

                if ($selected->id !== $usOutpost->id) {
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
            'latitude' => 42.3601,
            'longitude' => -71.0589,
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
            $this->assertNotNull($result);
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
            'latitude' => 42.3601,
            'longitude' => -71.0589,
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

    public function test_it_stores_closest_outpost_id(): void
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
            'latitude' => 42.3601,
            'longitude' => -71.0589,
        ]);

        $closestOutpost = Outpost::query()->create([
            'ip' => '192.168.1.1',
            'port' => 8080,
            'external_ip' => '1.2.3.4',
            'status' => OutpostStatus::Available,
            'country' => 'US',
            'latitude' => 42.3601,
            'longitude' => -71.0589, // Same location as monitor
            'last_available_at' => now(),
        ]);

        $farOutpost = Outpost::query()->create([
            'ip' => '192.168.1.2',
            'port' => 8080,
            'external_ip' => '1.2.3.5',
            'status' => OutpostStatus::Available,
            'country' => 'UK',
            'latitude' => 51.5074,
            'longitude' => -0.1278,
            'last_available_at' => now(),
        ]);

        $determineOutpost = new DetermineOutpost;

        // Initially, no closest outpost is set
        $this->assertNull($monitor->closest_outpost_id);

        // Determine outpost should store the closest one
        $result = $determineOutpost->determine($monitor);

        $monitor->refresh();
        $this->assertNotNull($monitor->closest_outpost_id);
        $this->assertEquals($closestOutpost->id, $monitor->closest_outpost_id);
    }

    public function test_it_nullifies_closest_outpost_when_outpost_deleted(): void
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
            'latitude' => 42.3601,
            'longitude' => -71.0589,
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

        // Set the closest outpost
        $monitor->update(['closest_outpost_id' => $outpost->id]);

        // Delete the outpost
        $outpost->delete();

        // Verify the closest_outpost_id is set to null
        $monitor->refresh();
        $this->assertNull($monitor->closest_outpost_id);
    }

    public function test_it_nullifies_closest_outpost_when_outpost_becomes_unavailable(): void
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
            'latitude' => 42.3601,
            'longitude' => -71.0589,
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

        // Set the closest outpost
        $monitor->update(['closest_outpost_id' => $outpost->id]);

        // Mark the outpost as unavailable
        $outpost->update(['status' => OutpostStatus::Unavailable]);

        // Verify the closest_outpost_id is set to null
        $monitor->refresh();
        $this->assertNull($monitor->closest_outpost_id);
    }

    public function test_it_uses_cached_closest_outpost_when_available(): void
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
            'latitude' => 42.3601,
            'longitude' => -71.0589,
        ]);

        $closestOutpost = Outpost::query()->create([
            'ip' => '192.168.1.1',
            'port' => 8080,
            'external_ip' => '1.2.3.4',
            'status' => OutpostStatus::Available,
            'country' => 'US',
            'latitude' => 42.3601,
            'longitude' => -71.0589,
            'last_available_at' => now(),
        ]);

        $remoteOutpost = Outpost::query()->create([
            'ip' => '192.168.1.2',
            'port' => 8080,
            'external_ip' => '1.2.3.5',
            'status' => OutpostStatus::Available,
            'country' => 'UK',
            'latitude' => 51.5074,
            'longitude' => -0.1278,
            'last_available_at' => now(),
        ]);

        // Pre-set the closest outpost
        $monitor->update(['closest_outpost_id' => $closestOutpost->id]);

        $determineOutpost = new DetermineOutpost;

        // When selecting closest, it should use the cached value
        $closestSelections = 0;
        for ($i = 0; $i < 100; $i++) {
            $result = $determineOutpost->determine($monitor);
            if ($result !== null && $result->id === $closestOutpost->id) {
                $closestSelections++;
            }
        }

        // Should use the cached closest outpost approximately 50% of the time
        $this->assertGreaterThan(30, $closestSelections);
        $this->assertLessThan(70, $closestSelections);
    }

    public function test_excluded_outposts_dont_affect_closest_cache(): void
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
            'latitude' => 42.3601,
            'longitude' => -71.0589,
        ]);

        $closestOutpost = Outpost::query()->create([
            'ip' => '192.168.1.1',
            'port' => 8080,
            'external_ip' => '1.2.3.4',
            'status' => OutpostStatus::Available,
            'country' => 'US',
            'latitude' => 42.3601,
            'longitude' => -71.0589, // Same as monitor (closest)
            'last_available_at' => now(),
        ]);

        $secondClosest = Outpost::query()->create([
            'ip' => '192.168.1.2',
            'port' => 8080,
            'external_ip' => '1.2.3.5',
            'status' => OutpostStatus::Available,
            'country' => 'US',
            'latitude' => 42.3650,
            'longitude' => -71.0600, // Very close to monitor
            'last_available_at' => now(),
        ]);

        $remoteOutpost = Outpost::query()->create([
            'ip' => '192.168.1.3',
            'port' => 8080,
            'external_ip' => '1.2.3.6',
            'status' => OutpostStatus::Available,
            'country' => 'UK',
            'latitude' => 51.5074,
            'longitude' => -0.1278,
            'last_available_at' => now(),
        ]);

        $determineOutpost = new DetermineOutpost;

        // First call should set the closest outpost
        $determineOutpost->determine($monitor);
        $monitor->refresh();
        $this->assertEquals($closestOutpost->id, $monitor->closest_outpost_id);

        // When we exclude the closest outpost (simulating retry after failure),
        // it should NOT return the excluded outpost
        // Test multiple times to account for randomness in selection
        $excludedReturned = false;
        for ($i = 0; $i < 10; $i++) {
            $result = $determineOutpost->determine($monitor, [$closestOutpost->id]);

            // Should never get the excluded closest outpost
            if ($result !== null && $result->id === $closestOutpost->id) {
                $excludedReturned = true;
                break;
            }
        }

        $this->assertFalse($excludedReturned, 'Excluded outpost should never be returned');

        // The cached closest_outpost_id should NOT have changed
        $monitor->refresh();
        $this->assertEquals($closestOutpost->id, $monitor->closest_outpost_id,
            'Closest outpost cache should not change when using excluded outposts');
    }
}
