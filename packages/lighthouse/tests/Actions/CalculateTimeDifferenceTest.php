<?php

namespace Vigilant\Lighthouse\Tests\Actions;

use PHPUnit\Framework\Attributes\Test;
use Vigilant\Lighthouse\Actions\CalculateTimeDifference;
use Vigilant\Lighthouse\Models\LighthouseMonitor;
use Vigilant\Lighthouse\Tests\TestCase;

class CalculateTimeDifferenceTest extends TestCase
{
    #[Test]
    public function it_calculates_difference(): void
    {
        /** @var LighthouseMonitor $site */
        $site = LighthouseMonitor::query()->create([
            'team_id' => 1,
            'url' => 'https://govigilant.io',
            'settings' => [],
            'interval' => '0 * * * *',
        ]);

        $site->lighthouseResults()->create([
            'performance' => 1,
            'accessibility' => 1,
            'best_practices' => 1,
            'seo' => 1,
            'created_at' => now()->subHours(24),
            'updated_at' => now()->subHours(24),
        ]);

        $site->lighthouseResults()->create([
            'performance' => 1,
            'accessibility' => 1,
            'best_practices' => 1,
            'seo' => 1,
            'created_at' => now()->subHours(23),
            'updated_at' => now()->subHours(23),
        ]);

        $site->lighthouseResults()->create([
            'performance' => 1,
            'accessibility' => 1,
            'best_practices' => 1,
            'seo' => 1,
            'created_at' => now()->subHours(22),
            'updated_at' => now()->subHours(22),
        ]);

        $site->lighthouseResults()->create([
            'performance' => 1,
            'accessibility' => 1,
            'best_practices' => 1,
            'seo' => 1,
            'created_at' => now()->subHours(21),
            'updated_at' => now()->subHours(21),
        ]);

        $site->lighthouseResults()->create([
            'performance' => 1,
            'accessibility' => 1,
            'best_practices' => 1,
            'seo' => 1,
            'created_at' => now()->subHours(20),
            'updated_at' => now()->subHours(20),
        ]);

        $site->lighthouseResults()->create([
            'performance' => 1,
            'accessibility' => 1,
            'best_practices' => 1,
            'seo' => 1,
            'created_at' => now()->subHours(19),
            'updated_at' => now()->subHours(19),
        ]);

        $site->lighthouseResults()->create([
            'performance' => 1,
            'accessibility' => 1,
            'best_practices' => 1,
            'seo' => 1,
            'created_at' => now()->subHours(18),
            'updated_at' => now()->subHours(18),
        ]);

        $site->lighthouseResults()->create([
            'performance' => 1,
            'accessibility' => 1,
            'best_practices' => 1,
            'seo' => 1,
            'created_at' => now()->subHours(17),
            'updated_at' => now()->subHours(17),
        ]);

        $site->lighthouseResults()->create([
            'performance' => 1,
            'accessibility' => 1,
            'best_practices' => 1,
            'seo' => 1,
            'created_at' => now()->subHours(16),
            'updated_at' => now()->subHours(16),
        ]);

        $site->lighthouseResults()->create([
            'performance' => 0.5,
            'accessibility' => 0.5,
            'best_practices' => 0.5,
            'seo' => 0.5,
            'created_at' => now()->subHours(15),
            'updated_at' => now()->subHours(15),
        ]);

        $site->lighthouseResults()->create([
            'performance' => 0.5,
            'accessibility' => 0.5,
            'best_practices' => 0.5,
            'seo' => 0.5,
            'created_at' => now()->subHours(14),
            'updated_at' => now()->subHours(14),
        ]);

        /** @var CalculateTimeDifference $action */
        $action = app(CalculateTimeDifference::class);
        $result = $action->calculate($site, now()->subHours(24));

        $this->assertNotNull($result);
        $this->assertEquals(0.5, $result->performanceNew());
        $this->assertEquals(1, $result->performanceOld());
        $this->assertEquals(-50, $result->performanceDifference());
    }
}
