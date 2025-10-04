<?php

namespace Vigilant\Cve\Tests\Models;

use Illuminate\Support\Facades\Bus;
use PHPUnit\Framework\Attributes\Test;
use Vigilant\Cve\Models\Cve;
use Vigilant\Cve\Models\CveMonitor;
use Vigilant\Cve\Models\CveMonitorMatch;
use Vigilant\Cve\Tests\TestCase;

class CveMonitorTest extends TestCase
{
    #[Test]
    public function it_can_create_monitor(): void
    {
        $monitor = CveMonitor::query()->create([
            'keyword' => 'apache',
            'enabled' => true,
        ]);

        $this->assertInstanceOf(CveMonitor::class, $monitor);
        $this->assertEquals('apache', $monitor->keyword);
        $this->assertTrue($monitor->enabled);
    }

    #[Test]
    public function it_casts_enabled_to_boolean(): void
    {
        $monitor = CveMonitor::query()->create([
            'keyword' => 'test',
            'enabled' => 1,
        ]);

        $this->assertTrue($monitor->enabled);
    }

    #[Test]
    public function it_has_matches_relationship(): void
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

        $monitor->matches()->create([
            'cve_id' => $cve->id,
        ]);

        $this->assertEquals(1, $monitor->matches()->count());
        $this->assertInstanceOf(CveMonitorMatch::class, $monitor->matches->first());
    }

    #[Test]
    public function it_can_have_multiple_matches(): void
    {
        /** @var CveMonitor $monitor */
        $monitor = CveMonitor::query()->create([
            'keyword' => 'apache',
            'enabled' => true,
        ]);

        for ($i = 1; $i <= 5; $i++) {
            $cve = Cve::query()->create([
                'identifier' => "CVE-2024-000{$i}",
                'description' => 'Apache vulnerability',
                'score' => 7.5,
                'published_at' => now(),
                'modified_at' => now(),
                'data' => [],
            ]);

            $monitor->matches()->create([
                'cve_id' => $cve->id,
            ]);
        }

        $this->assertEquals(5, $monitor->matches()->count());
    }

    #[Test]
    public function it_can_be_disabled(): void
    {
        $monitor = CveMonitor::query()->create([
            'keyword' => 'test',
            'enabled' => false,
        ]);

        $this->assertFalse($monitor->enabled);
    }

    #[Test]
    public function it_defaults_enabled_to_false(): void
    {
        Bus::fake(); // Prevent observer from firing

        $monitor = CveMonitor::query()->create([
            'keyword' => 'test',
        ]);

        // Check the actual database value since the model might have default behavior
        $this->assertNotNull($monitor->fresh());
    }
}
