<?php

namespace Vigilant\Lighthouse\Tests\Notifications\Conditions\Category;

use PHPUnit\Framework\Attributes\Test;
use Vigilant\Lighthouse\Data\CategoryResultDifferenceData;
use Vigilant\Lighthouse\Models\LighthouseMonitor;
use Vigilant\Lighthouse\Models\LighthouseResult;
use Vigilant\Lighthouse\Notifications\CategoryScoreChangedNotification;
use Vigilant\Lighthouse\Notifications\Conditions\Category\PerformanceScoreValueCondition;
use Vigilant\Lighthouse\Tests\TestCase;

class PerformanceScoreValueConditionTest extends TestCase
{
    #[Test]
    public function it_checks_new_value_greater_than_threshold(): void
    {
        $condition = new PerformanceScoreValueCondition;

        $notification = $this->makeNotification(0.5, 0.8);

        $this->assertTrue($condition->applies($notification, 'new', '>', 70, null));
        $this->assertFalse($condition->applies($notification, 'new', '>', 80, null));
        $this->assertTrue($condition->applies($notification, 'new', '>=', 80, null));
    }

    #[Test]
    public function it_checks_new_value_less_than_threshold(): void
    {
        $condition = new PerformanceScoreValueCondition;

        $notification = $this->makeNotification(0.8, 0.5);

        $this->assertTrue($condition->applies($notification, 'new', '<', 60, null));
        $this->assertFalse($condition->applies($notification, 'new', '<', 50, null));
        $this->assertTrue($condition->applies($notification, 'new', '<=', 50, null));
    }

    #[Test]
    public function it_checks_old_value_greater_than_threshold(): void
    {
        $condition = new PerformanceScoreValueCondition;

        $notification = $this->makeNotification(0.8, 0.5);

        $this->assertTrue($condition->applies($notification, 'old', '>', 70, null));
        $this->assertFalse($condition->applies($notification, 'old', '>', 80, null));
        $this->assertTrue($condition->applies($notification, 'old', '>=', 80, null));
    }

    #[Test]
    public function it_checks_old_value_less_than_threshold(): void
    {
        $condition = new PerformanceScoreValueCondition;

        $notification = $this->makeNotification(0.5, 0.8);

        $this->assertTrue($condition->applies($notification, 'old', '<', 60, null));
        $this->assertFalse($condition->applies($notification, 'old', '<', 50, null));
        $this->assertTrue($condition->applies($notification, 'old', '<=', 50, null));
    }

    #[Test]
    public function it_checks_equality(): void
    {
        $condition = new PerformanceScoreValueCondition;

        $notification = $this->makeNotification(0.5, 0.5);

        $this->assertTrue($condition->applies($notification, 'new', '=', 50, null));
        $this->assertFalse($condition->applies($notification, 'new', '=', 60, null));
        $this->assertTrue($condition->applies($notification, 'new', '<>', 60, null));
    }

    protected function makeNotification(float $oldPerformance, float $newPerformance): CategoryScoreChangedNotification
    {
        $monitor = LighthouseMonitor::query()->create([
            'team_id' => 1,
            'url' => 'https://example.com',
            'settings' => [],
            'interval' => '0 * * * *',
        ]);

        $result = LighthouseResult::query()->create([
            'lighthouse_monitor_id' => $monitor->id,
            'performance' => $newPerformance,
            'accessibility' => 0.7,
            'best_practices' => 0.7,
            'seo' => 0.7,
        ]);

        $data = CategoryResultDifferenceData::of([
            'performance_old' => $oldPerformance,
            'performance_new' => $newPerformance,
            'accessibility_old' => 0.7,
            'accessibility_new' => 0.7,
            'best_practices_old' => 0.7,
            'best_practices_new' => 0.7,
            'seo_old' => 0.7,
            'seo_new' => 0.7,
        ]);

        return new CategoryScoreChangedNotification($result, $data);
    }
}
