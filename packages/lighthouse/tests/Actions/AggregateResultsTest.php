<?php

namespace Vigilant\Lighthouse\Tests\Actions;

use PHPUnit\Framework\Attributes\Test;
use Vigilant\Lighthouse\Actions\AggregateResults;
use Vigilant\Lighthouse\Models\LighthouseSite;
use Vigilant\Lighthouse\Tests\TestCase;

class AggregateResultsTest extends TestCase
{
    #[Test]
    public function it_aggregates_result(): void
    {
        /** @var LighthouseSite $site */
        $site = LighthouseSite::query()->create([
            'team_id' => 1,
            'url' => 'https://govigilant.io',
            'settings' => [],
            'interval' => '0 * * * *',
        ]);

        for ($i = 0; $i < 5; $i++) {
            $site->lighthouseResults()->create([
                'performance' => 1,
                'accessibility' => 1,
                'best_practices' => 1,
                'seo' => 1,
                'created_at' => now()->subHours($i),
                'updated_at' => now()->subHours($i),
            ]);
        }

        for ($i = 5; $i < 10; $i++) {
            $site->lighthouseResults()->create([
                'performance' => 0.5,
                'accessibility' => 0.5,
                'best_practices' => 0.5,
                'seo' => 0.5,
                'created_at' => now()->subHours($i),
                'updated_at' => now()->subHours($i),
            ]);
        }

        /** @var AggregateResults $action */
        $action = app(AggregateResults::class);

        $action->aggregate($site, now()->subHours(10), now());

        $this->assertCount(1, $site->lighthouseResults);
        $this->assertEquals(0.75, $site->lighthouseResults->first()->performance);
        $this->assertEquals(0.75, $site->lighthouseResults->first()->accessibility);
        $this->assertEquals(0.75, $site->lighthouseResults->first()->best_practices);
        $this->assertEquals(0.75, $site->lighthouseResults->first()->seo);
    }
}
