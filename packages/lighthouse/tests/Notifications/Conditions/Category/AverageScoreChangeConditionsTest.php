<?php

namespace Vigilant\Lighthouse\Tests\Notifications\Conditions\Category;

use PHPUnit\Framework\Attributes\Test;
use Vigilant\Lighthouse\Data\CategoryResultDifferenceData;
use Vigilant\Lighthouse\Models\LighthouseMonitor;
use Vigilant\Lighthouse\Models\LighthouseResult;
use Vigilant\Lighthouse\Notifications\CategoryScoreChangedNotification;
use Vigilant\Lighthouse\Notifications\Conditions\Category\AverageScoreChangesCondition;
use Vigilant\Lighthouse\Notifications\Conditions\Category\AverageScoreDecreasesCondition;
use Vigilant\Lighthouse\Notifications\Conditions\Category\AverageScoreIncreasesCondition;
use Vigilant\Lighthouse\Tests\TestCase;

class AverageScoreChangeConditionsTest extends TestCase
{
    #[Test]
    public function it_checks_score_increases(): void
    {
        $condition = new AverageScoreIncreasesCondition;

        // 20% increase
        $notification = $this->makeNotification(0.5, 0.6);

        $this->assertTrue($condition->applies($notification, null, '>=', 15, null));
        $this->assertTrue($condition->applies($notification, null, '>=', 20, null));
        $this->assertFalse($condition->applies($notification, null, '>=', 25, null));
        $this->assertTrue($condition->applies($notification, null, '>', 15, null));
        $this->assertFalse($condition->applies($notification, null, '>', 20, null));
    }

    #[Test]
    public function it_checks_score_decreases(): void
    {
        $condition = new AverageScoreDecreasesCondition;

        // 20% decrease
        $notification = $this->makeNotification(0.6, 0.5);

        $this->assertTrue($condition->applies($notification, null, '>=', 15, null));
        $this->assertTrue($condition->applies($notification, null, '>=', 16.7, null));
        $this->assertFalse($condition->applies($notification, null, '>=', 20, null));
        $this->assertTrue($condition->applies($notification, null, '>', 15, null));
        $this->assertFalse($condition->applies($notification, null, '>', 16.7, null));
    }

    #[Test]
    public function it_checks_score_changes_either_direction(): void
    {
        $conditionIncrease = new AverageScoreChangesCondition;
        $conditionDecrease = new AverageScoreChangesCondition;

        $notificationIncrease = $this->makeNotification(0.5, 0.6);
        $notificationDecrease = $this->makeNotification(0.6, 0.5);

        $this->assertTrue($conditionIncrease->applies($notificationIncrease, null, '>=', 15, null));
        $this->assertTrue($conditionDecrease->applies($notificationDecrease, null, '>=', 15, null));
        
        $this->assertTrue($conditionIncrease->applies($notificationIncrease, null, '>', 15, null));
        $this->assertFalse($conditionIncrease->applies($notificationIncrease, null, '>', 20, null));
    }

    #[Test]
    public function it_does_not_trigger_increase_on_decrease(): void
    {
        $condition = new AverageScoreIncreasesCondition;
        $notification = $this->makeNotification(0.6, 0.5);

        $this->assertFalse($condition->applies($notification, null, '>=', 10, null));
    }

    #[Test]
    public function it_does_not_trigger_decrease_on_increase(): void
    {
        $condition = new AverageScoreDecreasesCondition;
        $notification = $this->makeNotification(0.5, 0.6);

        $this->assertFalse($condition->applies($notification, null, '>=', 10, null));
    }

    protected function makeNotification(float $oldScore, float $newScore): CategoryScoreChangedNotification
    {
        $monitor = LighthouseMonitor::query()->create([
            'team_id' => 1,
            'url' => 'https://example.com',
            'settings' => [],
            'interval' => '0 * * * *',
        ]);

        $result = LighthouseResult::query()->create([
            'lighthouse_monitor_id' => $monitor->id,
            'performance' => $newScore,
            'accessibility' => $newScore,
            'best_practices' => $newScore,
            'seo' => $newScore,
        ]);

        $data = CategoryResultDifferenceData::of([
            'performance_old' => $oldScore,
            'performance_new' => $newScore,
            'accessibility_old' => $oldScore,
            'accessibility_new' => $newScore,
            'best_practices_old' => $oldScore,
            'best_practices_new' => $newScore,
            'seo_old' => $oldScore,
            'seo_new' => $newScore,
        ]);

        return new CategoryScoreChangedNotification($result, $data);
    }
}
