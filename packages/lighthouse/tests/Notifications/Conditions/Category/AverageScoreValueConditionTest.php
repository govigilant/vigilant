<?php

namespace Vigilant\Lighthouse\Tests\Notifications\Conditions\Category;

use PHPUnit\Framework\Attributes\Test;
use Vigilant\Lighthouse\Data\CategoryResultDifferenceData;
use Vigilant\Lighthouse\Models\LighthouseMonitor;
use Vigilant\Lighthouse\Models\LighthouseResult;
use Vigilant\Lighthouse\Notifications\CategoryScoreChangedNotification;
use Vigilant\Lighthouse\Notifications\Conditions\Category\AverageScoreValueCondition;
use Vigilant\Lighthouse\Tests\TestCase;

class AverageScoreValueConditionTest extends TestCase
{
    #[Test]
    public function it_checks_new_average_value_greater_than_threshold(): void
    {
        $condition = new AverageScoreValueCondition;

        $notification = $this->makeNotification([0.5, 0.6, 0.7, 0.8], [0.7, 0.8, 0.8, 0.9]);

        // Average new: (70 + 80 + 80 + 90) / 4 = 80
        $this->assertTrue($condition->applies($notification, 'new', '>', 75, null));
        $this->assertFalse($condition->applies($notification, 'new', '>', 80, null));
        $this->assertTrue($condition->applies($notification, 'new', '>=', 80, null));
    }

    #[Test]
    public function it_checks_new_average_value_less_than_threshold(): void
    {
        $condition = new AverageScoreValueCondition;

        $notification = $this->makeNotification([0.7, 0.8, 0.8, 0.9], [0.4, 0.5, 0.5, 0.6]);

        // Average new: (40 + 50 + 50 + 60) / 4 = 50
        $this->assertTrue($condition->applies($notification, 'new', '<', 60, null));
        $this->assertFalse($condition->applies($notification, 'new', '<', 50, null));
        $this->assertTrue($condition->applies($notification, 'new', '<=', 50, null));
    }

    #[Test]
    public function it_checks_old_average_value_greater_than_threshold(): void
    {
        $condition = new AverageScoreValueCondition;

        $notification = $this->makeNotification([0.7, 0.8, 0.8, 0.9], [0.4, 0.5, 0.5, 0.6]);

        // Average old: (70 + 80 + 80 + 90) / 4 = 80
        $this->assertTrue($condition->applies($notification, 'old', '>', 75, null));
        $this->assertFalse($condition->applies($notification, 'old', '>', 80, null));
        $this->assertTrue($condition->applies($notification, 'old', '>=', 80, null));
    }

    #[Test]
    public function it_checks_equality(): void
    {
        $condition = new AverageScoreValueCondition;

        $notification = $this->makeNotification([0.5, 0.5, 0.5, 0.5], [0.6, 0.6, 0.6, 0.6]);

        $this->assertTrue($condition->applies($notification, 'new', '=', 60, null));
        $this->assertFalse($condition->applies($notification, 'new', '=', 50, null));
        $this->assertTrue($condition->applies($notification, 'new', '<>', 50, null));
    }

    protected function makeNotification(array $oldScores, array $newScores): CategoryScoreChangedNotification
    {
        $monitor = LighthouseMonitor::query()->create([
            'team_id' => 1,
            'url' => 'https://example.com',
            'settings' => [],
            'interval' => '0 * * * *',
        ]);

        $result = LighthouseResult::query()->create([
            'lighthouse_monitor_id' => $monitor->id,
            'performance' => $newScores[0],
            'accessibility' => $newScores[1],
            'best_practices' => $newScores[2],
            'seo' => $newScores[3],
        ]);

        $data = CategoryResultDifferenceData::of([
            'performance_old' => $oldScores[0],
            'performance_new' => $newScores[0],
            'accessibility_old' => $oldScores[1],
            'accessibility_new' => $newScores[1],
            'best_practices_old' => $oldScores[2],
            'best_practices_new' => $newScores[2],
            'seo_old' => $oldScores[3],
            'seo_new' => $newScores[3],
        ]);

        return new CategoryScoreChangedNotification($result, $data);
    }
}
