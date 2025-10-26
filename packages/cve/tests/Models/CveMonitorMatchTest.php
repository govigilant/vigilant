<?php

namespace Vigilant\Cve\Tests\Models;

use PHPUnit\Framework\Attributes\Test;
use Vigilant\Cve\Models\Cve;
use Vigilant\Cve\Models\CveMonitor;
use Vigilant\Cve\Models\CveMonitorMatch;
use Vigilant\Cve\Tests\TestCase;

class CveMonitorMatchTest extends TestCase
{
    #[Test]
    public function it_can_create_match(): void
    {
        /** @var CveMonitor $monitor */
        $monitor = CveMonitor::query()->create([
            'keyword' => 'apache',
            'enabled' => true,
        ]);

        /** @var Cve $cve */
        $cve = Cve::query()->create([
            'identifier' => 'CVE-2024-0001',
            'description' => 'Apache vulnerability',
            'score' => 7.5,
            'published_at' => now(),
            'modified_at' => now(),
            'data' => [],
        ]);

        $match = CveMonitorMatch::query()->create([
            'cve_monitor_id' => $monitor->id,
            'cve_id' => $cve->id,
        ]);

        $this->assertInstanceOf(CveMonitorMatch::class, $match);
        $this->assertEquals($monitor->id, $match->cve_monitor_id);
        $this->assertEquals($cve->id, $match->cve_id);
    }

    #[Test]
    public function it_belongs_to_cve(): void
    {
        /** @var CveMonitor $monitor */
        $monitor = CveMonitor::query()->create([
            'keyword' => 'test',
            'enabled' => true,
        ]);

        /** @var Cve $cve */
        $cve = Cve::query()->create([
            'identifier' => 'CVE-2024-0001',
            'description' => 'Test vulnerability',
            'score' => 5.0,
            'published_at' => now(),
            'modified_at' => now(),
            'data' => [],
        ]);

        $match = CveMonitorMatch::query()->create([
            'cve_monitor_id' => $monitor->id,
            'cve_id' => $cve->id,
        ]);

        $this->assertInstanceOf(Cve::class, $match->cve);
        $this->assertEquals('CVE-2024-0001', $match->cve->identifier);
    }

    #[Test]
    public function it_belongs_to_cve_monitor(): void
    {
        /** @var CveMonitor $monitor */
        $monitor = CveMonitor::query()->create([
            'keyword' => 'apache',
            'enabled' => true,
        ]);

        /** @var Cve $cve */
        $cve = Cve::query()->create([
            'identifier' => 'CVE-2024-0001',
            'description' => 'Apache vulnerability',
            'score' => 7.5,
            'published_at' => now(),
            'modified_at' => now(),
            'data' => [],
        ]);

        $match = CveMonitorMatch::query()->create([
            'cve_monitor_id' => $monitor->id,
            'cve_id' => $cve->id,
        ]);

        $this->assertInstanceOf(CveMonitor::class, $match->cveMonitor);
        $this->assertEquals('apache', $match->cveMonitor->keyword);
    }

    #[Test]
    public function it_has_timestamps(): void
    {
        /** @var CveMonitor $monitor */
        $monitor = CveMonitor::query()->create([
            'keyword' => 'test',
            'enabled' => true,
        ]);

        /** @var Cve $cve */
        $cve = Cve::query()->create([
            'identifier' => 'CVE-2024-0001',
            'description' => 'Test',
            'score' => 5.0,
            'published_at' => now(),
            'modified_at' => now(),
            'data' => [],
        ]);

        $match = CveMonitorMatch::query()->create([
            'cve_monitor_id' => $monitor->id,
            'cve_id' => $cve->id,
        ]);

        $this->assertNotNull($match->created_at);
        $this->assertNotNull($match->updated_at);
    }
}
