<?php

namespace Vigilant\Lighthouse\Tests\Actions;

use PHPUnit\Framework\Attributes\Test;
use Vigilant\Lighthouse\Actions\CheckLighthouseResultAudit;
use Vigilant\Lighthouse\Models\LighthouseMonitor;
use Vigilant\Lighthouse\Models\LighthouseResult;
use Vigilant\Lighthouse\Models\LighthouseResultAudit;
use Vigilant\Lighthouse\Notifications\NumericAuditChangedNotification;
use Vigilant\Lighthouse\Tests\TestCase;

class CheckLighthouseResultAuditTest extends TestCase
{
    #[Test]
    public function it_dispatches_notification(): void
    {
        NumericAuditChangedNotification::fake();

        /** @var LighthouseMonitor $site */
        $site = LighthouseMonitor::query()->create([
            'team_id' => 1,
            'url' => 'https://govigilant.io',
            'settings' => [],
            'interval' => '0 * * * *',
        ]);

        /** @var LighthouseResult $result */
        $result = $site->lighthouseResults()->create([
            'performance' => 0.5,
            'accessibility' => 0.5,
            'best_practices' => 0.5,
            'seo' => 0.5,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        for ($i = 0; $i < 100; $i++) {
            $result->audits()->create([
                'audit' => 'test',
                'title' => 'test',
                'explanation' => 'test',
                'description' => 'test',
                'score' => 1,
                'scoreDisplayMode' => 'numeric',
                'numericValue' => $i * 10,
            ]);
        }

        /** @var LighthouseResultAudit $audit */
        $audit = $result->audits()->create([
            'audit' => 'test',
            'title' => 'test',
            'explanation' => 'test',
            'description' => 'test',
            'score' => 1,
            'scoreDisplayMode' => 'numeric',
            'numericValue' => 100,
        ]);

        /** @var CheckLighthouseResultAudit $action */
        $action = app(CheckLighthouseResultAudit::class);

        $action->check($audit);

        $this->assertTrue(NumericAuditChangedNotification::wasDispatched(function (
            NumericAuditChangedNotification $notification
        ): bool {
            return round($notification->percentChanged) == 32;
        }));
    }
}
