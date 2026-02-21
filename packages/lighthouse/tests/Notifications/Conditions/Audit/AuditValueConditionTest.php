<?php

namespace Vigilant\Lighthouse\Tests\Notifications\Conditions\Audit;

use PHPUnit\Framework\Attributes\Test;
use Vigilant\Lighthouse\Models\LighthouseMonitor;
use Vigilant\Lighthouse\Models\LighthouseResult;
use Vigilant\Lighthouse\Models\LighthouseResultAudit;
use Vigilant\Lighthouse\Notifications\Conditions\Audit\AuditValueCondition;
use Vigilant\Lighthouse\Notifications\NumericAuditChangedNotification;
use Vigilant\Lighthouse\Tests\TestCase;

class AuditValueConditionTest extends TestCase
{
    #[Test]
    public function it_checks_new_value_greater_than_threshold(): void
    {
        $condition = new AuditValueCondition;

        $notification = $this->makeNotification(100, 150, 50);

        $this->assertTrue($condition->applies($notification, 'new', '>', 140, null));
        $this->assertFalse($condition->applies($notification, 'new', '>', 150, null));
        $this->assertTrue($condition->applies($notification, 'new', '>=', 150, null));
    }

    #[Test]
    public function it_checks_new_value_less_than_threshold(): void
    {
        $condition = new AuditValueCondition;

        $notification = $this->makeNotification(150, 100, -33.33);

        $this->assertTrue($condition->applies($notification, 'new', '<', 110, null));
        $this->assertFalse($condition->applies($notification, 'new', '<', 100, null));
        $this->assertTrue($condition->applies($notification, 'new', '<=', 100, null));
    }

    #[Test]
    public function it_checks_old_value_greater_than_threshold(): void
    {
        $condition = new AuditValueCondition;

        $notification = $this->makeNotification(150, 100, -33.33);

        $this->assertTrue($condition->applies($notification, 'old', '>', 140, null));
        $this->assertFalse($condition->applies($notification, 'old', '>', 150, null));
        $this->assertTrue($condition->applies($notification, 'old', '>=', 150, null));
    }

    #[Test]
    public function it_checks_old_value_less_than_threshold(): void
    {
        $condition = new AuditValueCondition;

        $notification = $this->makeNotification(100, 150, 50);

        $this->assertTrue($condition->applies($notification, 'old', '<', 110, null));
        $this->assertFalse($condition->applies($notification, 'old', '<', 100, null));
        $this->assertTrue($condition->applies($notification, 'old', '<=', 100, null));
    }

    #[Test]
    public function it_checks_equality(): void
    {
        $condition = new AuditValueCondition;

        $notification = $this->makeNotification(100, 100, 0);

        $this->assertTrue($condition->applies($notification, 'new', '=', 100, null));
        $this->assertFalse($condition->applies($notification, 'new', '=', 150, null));
        $this->assertTrue($condition->applies($notification, 'new', '<>', 150, null));
    }

    protected function makeNotification(float $previous, float $current, float $percentChanged): NumericAuditChangedNotification
    {
        $monitor = LighthouseMonitor::query()->create([
            'team_id' => 1,
            'url' => 'https://example.com',
            'settings' => [],
            'interval' => '0 * * * *',
        ]);

        $result = LighthouseResult::query()->create([
            'lighthouse_monitor_id' => $monitor->id,
            'performance' => 0.7,
            'accessibility' => 0.7,
            'best_practices' => 0.7,
            'seo' => 0.7,
        ]);

        $audit = LighthouseResultAudit::query()->create([
            'lighthouse_result_id' => $result->id,
            'team_id' => 1,
            'audit' => 'first-contentful-paint',
            'title' => 'First Contentful Paint',
            'description' => 'Test audit',
            'score' => 0.5,
            'scoreDisplayMode' => 'numeric',
            'numericValue' => $current,
            'numericUnit' => 'millisecond',
        ]);

        return new NumericAuditChangedNotification($audit, $percentChanged, $previous, $current);
    }
}
