<?php

namespace Vigilant\Cve\Tests\Actions;

use PHPUnit\Framework\Attributes\Test;
use Vigilant\Cve\Actions\MatchCve;
use Vigilant\Cve\Models\Cve;
use Vigilant\Cve\Models\CveMonitor;
use Vigilant\Cve\Tests\TestCase;

class MatchCveTest extends TestCase
{
    #[Test]
    public function it_matches_cve_with_keyword(): void
    {
        /** @var CveMonitor $monitor */
        $monitor = CveMonitor::query()->create([
            'keyword' => 'apache',
            'enabled' => true,
        ]);

        /** @var Cve $cve */
        $cve = Cve::query()->create([
            'identifier' => 'CVE-2024-1234',
            'description' => 'Apache HTTP Server vulnerability allows remote code execution',
            'score' => 9.8,
            'published_at' => now(),
            'modified_at' => now(),
            'data' => [],
        ]);

        /** @var MatchCve $action */
        $action = app(MatchCve::class);
        $action->match($monitor, $cve);

        $this->assertDatabaseHas('cve_monitor_matches', [
            'cve_monitor_id' => $monitor->id,
            'cve_id' => $cve->id,
        ]);
    }

    #[Test]
    public function it_does_not_match_cve_without_keyword(): void
    {
        /** @var CveMonitor $monitor */
        $monitor = CveMonitor::query()->create([
            'keyword' => 'apache',
            'enabled' => true,
        ]);

        /** @var Cve $cve */
        $cve = Cve::query()->create([
            'identifier' => 'CVE-2024-5678',
            'description' => 'nginx vulnerability allows privilege escalation',
            'score' => 7.2,
            'published_at' => now(),
            'modified_at' => now(),
            'data' => [],
        ]);

        /** @var MatchCve $action */
        $action = app(MatchCve::class);
        $action->match($monitor, $cve);

        $this->assertDatabaseMissing('cve_monitor_matches', [
            'cve_monitor_id' => $monitor->id,
            'cve_id' => $cve->id,
        ]);
    }

    #[Test]
    public function it_matches_case_insensitively(): void
    {
        /** @var CveMonitor $monitor */
        $monitor = CveMonitor::query()->create([
            'keyword' => 'APACHE',
            'enabled' => true,
        ]);

        /** @var Cve $cve */
        $cve = Cve::query()->create([
            'identifier' => 'CVE-2024-9999',
            'description' => 'apache server issue',
            'score' => 5.0,
            'published_at' => now(),
            'modified_at' => now(),
            'data' => [],
        ]);

        /** @var MatchCve $action */
        $action = app(MatchCve::class);
        $action->match($monitor, $cve);

        $this->assertDatabaseHas('cve_monitor_matches', [
            'cve_monitor_id' => $monitor->id,
            'cve_id' => $cve->id,
        ]);
    }

    #[Test]
    public function it_does_not_create_duplicate_matches(): void
    {
        /** @var CveMonitor $monitor */
        $monitor = CveMonitor::query()->create([
            'keyword' => 'test',
            'enabled' => true,
        ]);

        /** @var Cve $cve */
        $cve = Cve::query()->create([
            'identifier' => 'CVE-2024-TEST',
            'description' => 'test vulnerability',
            'score' => 5.0,
            'published_at' => now(),
            'modified_at' => now(),
            'data' => [],
        ]);

        $monitor->matches()->create([
            'cve_id' => $cve->id,
        ]);

        /** @var MatchCve $action */
        $action = app(MatchCve::class);
        $action->match($monitor, $cve);

        $this->assertEquals(1, $monitor->matches()->count());
    }

    #[Test]
    public function it_matches_partial_keywords(): void
    {
        /** @var CveMonitor $monitor */
        $monitor = CveMonitor::query()->create([
            'keyword' => 'sql',
            'enabled' => true,
        ]);

        /** @var Cve $cve */
        $cve = Cve::query()->create([
            'identifier' => 'CVE-2024-SQL',
            'description' => 'MySQL server allows SQL injection attacks',
            'score' => 8.0,
            'published_at' => now(),
            'modified_at' => now(),
            'data' => [],
        ]);

        /** @var MatchCve $action */
        $action = app(MatchCve::class);
        $action->match($monitor, $cve);

        $this->assertDatabaseHas('cve_monitor_matches', [
            'cve_monitor_id' => $monitor->id,
            'cve_id' => $cve->id,
        ]);
    }
}
