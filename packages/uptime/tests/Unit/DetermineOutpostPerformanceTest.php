<?php

namespace Vigilant\Uptime\Tests\Unit;

use Vigilant\Uptime\Actions\Outpost\DetermineOutpost;
use Vigilant\Uptime\Enums\OutpostStatus;
use Vigilant\Uptime\Enums\Type;
use Vigilant\Uptime\Models\Monitor;
use Vigilant\Uptime\Models\Outpost;
use Vigilant\Uptime\Tests\TestCase;

class DetermineOutpostPerformanceTest extends TestCase
{
    public function test_it_efficiently_handles_thousands_of_outposts(): void
    {
        // Create a monitor
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

        // Create thousands of outposts efficiently
        $outpostsData = [];
        $countries = ['US', 'UK', 'DE', 'FR', 'JP', 'AU', 'CA', 'BR', 'IN', 'SG'];

        for ($i = 0; $i < 1000; $i++) {
            $country = $countries[$i % count($countries)];
            $outpostsData[] = [
                'ip' => "192.168.".floor($i / 256).".".($i % 256),
                'port' => 8080 + ($i % 100),
                'external_ip' => floor($i / 256).".".($i % 256).".1.1",
                'status' => OutpostStatus::Available->value,
                'country' => $country,
                'latitude' => 40.0 + ($i % 10),
                'longitude' => -70.0 + ($i % 10),
                'last_available_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // Batch insert for performance
        foreach (array_chunk($outpostsData, 500) as $chunk) {
            Outpost::query()->insert($chunk);
        }

        $determineOutpost = new DetermineOutpost;

        // Measure performance
        $startTime = microtime(true);

        $iterations = 100;
        for ($i = 0; $i < $iterations; $i++) {
            $outpost = $determineOutpost->determine($monitor);
            $this->assertNotNull($outpost);
        }

        $endTime = microtime(true);
        $avgTime = ($endTime - $startTime) / $iterations;

        // Should complete each selection in less than 10ms on average
        $this->assertLessThan(0.01, $avgTime, 'Average selection time should be less than 10ms');

        // Verify country distribution
        $usSelections = 0;
        $remoteSelections = 0;

        for ($i = 0; $i < 100; $i++) {
            $outpost = $determineOutpost->determine($monitor);
            if ($outpost->country === 'US') {
                $usSelections++;
            } else {
                $remoteSelections++;
            }
        }

        // Should still maintain roughly 50/50 distribution
        $this->assertGreaterThan(30, $usSelections);
        $this->assertGreaterThan(30, $remoteSelections);
    }

    public function test_it_uses_database_queries_not_memory(): void
    {
        // Create monitor and outposts
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

        // Create 100 outposts
        for ($i = 0; $i < 100; $i++) {
            Outpost::query()->create([
                'ip' => "192.168.1.{$i}",
                'port' => 8080,
                'external_ip' => "1.2.3.{$i}",
                'status' => OutpostStatus::Available,
                'country' => $i < 50 ? 'US' : 'UK',
                'latitude' => 40.0 + $i,
                'longitude' => -70.0 + $i,
                'last_available_at' => now(),
            ]);
        }

        // Enable query log
        \DB::enableQueryLog();

        $determineOutpost = new DetermineOutpost;
        $determineOutpost->determine($monitor);

        $queries = \DB::getQueryLog();

        // Should use efficient queries (1-2 queries max depending on path)
        // - 1 query if same country has results
        // - 2 queries if same country is empty (try same country, then fallback)
        // - 1 query for remote country selection
        $this->assertLessThanOrEqual(2, count($queries), 'Should use at most 2 queries');

        foreach ($queries as $query) {
            // Verify queries use LIMIT to avoid loading all records
            $sql = strtolower($query['query']);
            
            // All select queries should have a limit
            if (strpos($sql, 'select') !== false && strpos($sql, 'from') !== false) {
                $this->assertTrue(
                    strpos($sql, 'limit') !== false,
                    'Query should use LIMIT to avoid loading all records: '.$query['query']
                );
            }
        }
    }

    public function test_remote_selection_distributes_across_all_countries(): void
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

        // Create outposts in different countries
        $countries = ['UK', 'DE', 'FR', 'JP', 'AU'];
        foreach ($countries as $index => $country) {
            Outpost::query()->create([
                'ip' => "192.168.1.{$index}",
                'port' => 8080,
                'external_ip' => "1.2.3.{$index}",
                'status' => OutpostStatus::Available,
                'country' => $country,
                'latitude' => 40.0,
                'longitude' => -70.0,
                'last_available_at' => now(),
            ]);
        }

        $determineOutpost = new DetermineOutpost;
        $selectedCountries = [];

        // Run many selections to see distribution
        for ($i = 0; $i < 100; $i++) {
            $outpost = $determineOutpost->determine($monitor);
            if ($outpost->country !== 'US') {
                $selectedCountries[] = $outpost->country;
            }
        }

        // Should have selected from multiple different countries
        $uniqueCountries = array_unique($selectedCountries);
        $this->assertGreaterThan(1, count($uniqueCountries), 'Should select from multiple remote countries');
    }
}
