<?php

namespace Vigilant\Cve\Tests\Models;

use Illuminate\Support\Facades\Bus;
use PHPUnit\Framework\Attributes\Test;
use Vigilant\Cve\Models\Cve;
use Vigilant\Cve\Models\CveMonitor;
use Vigilant\Cve\Models\CveMonitorMatch;
use Vigilant\Cve\Tests\TestCase;

class CveTest extends TestCase
{
    #[Test]
    public function it_can_create_cve(): void
    {
        $cve = Cve::query()->create([
            'identifier' => 'CVE-2024-1234',
            'description' => 'Test vulnerability',
            'score' => 7.5,
            'published_at' => now(),
            'modified_at' => now(),
            'data' => ['test' => 'data'],
        ]);

        $this->assertInstanceOf(Cve::class, $cve);
        $this->assertEquals('CVE-2024-1234', $cve->identifier);
        $this->assertEquals(7.5, $cve->score);
        $this->assertNotEmpty($cve->data);
    }

    #[Test]
    public function it_casts_score_to_float(): void
    {
        $cve = Cve::query()->create([
            'identifier' => 'CVE-2024-5678',
            'description' => 'Test',
            'score' => '9.8',
            'published_at' => now(),
            'modified_at' => now(),
            'data' => [],
        ]);

        $this->assertEquals(9.8, $cve->score);
    }

    #[Test]
    public function it_casts_dates_correctly(): void
    {
        $cve = Cve::query()->create([
            'identifier' => 'CVE-2024-9999',
            'description' => 'Test',
            'score' => 5.0,
            'published_at' => '2024-01-01 00:00:00',
            'modified_at' => '2024-01-02 00:00:00',
            'data' => [],
        ]);

        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $cve->published_at);
        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $cve->modified_at);
    }

    #[Test]
    public function it_has_matches_relationship(): void
    {
        Bus::fake(); // Fake bus to prevent observer from firing

        /** @var Cve $cve */
        $cve = Cve::query()->create([
            'identifier' => 'CVE-2024-0001',
            'description' => 'Test vulnerability',
            'score' => 7.5,
            'published_at' => now(),
            'modified_at' => now(),
            'data' => [],
        ]);

        /** @var CveMonitor $monitor */
        $monitor = CveMonitor::query()->create([
            'keyword' => 'test',
            'enabled' => true,
        ]);

        $cve->matches()->create([
            'cve_monitor_id' => $monitor->id,
        ]);

        $this->assertEquals(1, $cve->matches()->count());
        $this->assertInstanceOf(CveMonitorMatch::class, $cve->matches->first());
    }

    #[Test]
    public function it_handles_null_score(): void
    {
        $cve = Cve::query()->create([
            'identifier' => 'CVE-2024-NULL',
            'description' => 'Test',
            'score' => null,
            'published_at' => now(),
            'modified_at' => now(),
            'data' => [],
        ]);

        $this->assertNull($cve->score);
    }

    #[Test]
    public function it_stores_json_data(): void
    {
        $data = [
            'cve' => [
                'id' => 'CVE-2024-JSON',
                'metrics' => ['cvssV3' => 7.5],
            ],
        ];

        $cve = Cve::query()->create([
            'identifier' => 'CVE-2024-JSON',
            'description' => 'Test',
            'score' => 7.5,
            'published_at' => now(),
            'modified_at' => now(),
            'data' => $data,
        ]);

        $this->assertEquals($data, $cve->data);
        $this->assertEquals('CVE-2024-JSON', $cve->data['cve']['id']);
    }
}
