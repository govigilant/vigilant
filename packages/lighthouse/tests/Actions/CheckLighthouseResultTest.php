<?php

namespace Vigilant\Lighthouse\Tests\Actions;

use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\Test;
use Vigilant\Lighthouse\Actions\CheckLighthouseResult;
use Vigilant\Lighthouse\Actions\CheckLighthouseResultAudit;
use Vigilant\Lighthouse\Models\LighthouseMonitor;
use Vigilant\Lighthouse\Models\LighthouseResult;
use Vigilant\Lighthouse\Notifications\CategoryScoreChangedNotification;
use Vigilant\Lighthouse\Tests\TestCase;

class CheckLighthouseResultTest extends TestCase
{
    #[Test]
    public function it_dispatches_notification(): void
    {
        CategoryScoreChangedNotification::fake();

        $this->mock(CheckLighthouseResultAudit::class, function (MockInterface $mock): void {
            $mock->shouldReceive('check')->andReturn();
        });

        /** @var LighthouseMonitor $site */
        $site = LighthouseMonitor::query()->create([
            'team_id' => 1,
            'url' => 'https://govigilant.io',
            'settings' => [],
            'interval' => '0 * * * *',
        ]);

        for ($i = 0; $i < 4; $i++) {
            $site->lighthouseResults()->create([
                'performance' => 0.5,
                'accessibility' => 0.5,
                'best_practices' => 0.5,
                'seo' => 0.5,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        for ($i = 0; $i < 12; $i++) {
            $site->lighthouseResults()->create([
                'performance' => 0.7,
                'accessibility' => 0.7,
                'best_practices' => 0.7,
                'seo' => 0.5,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        /** @var LighthouseResult $lastResult */
        $lastResult = $site->lighthouseResults->last();

        /** @var CheckLighthouseResult $action */
        $action = app(CheckLighthouseResult::class);

        $action->check($lastResult);

        $this->assertTrue(CategoryScoreChangedNotification::wasDispatched());
    }
}
