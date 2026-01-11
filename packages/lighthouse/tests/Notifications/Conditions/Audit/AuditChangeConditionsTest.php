<?php

namespace Vigilant\Lighthouse\Tests\Notifications\Conditions\Audit;

use PHPUnit\Framework\Attributes\Test;
use Vigilant\Lighthouse\Models\LighthouseMonitor;
use Vigilant\Lighthouse\Models\LighthouseResult;
use Vigilant\Lighthouse\Models\LighthouseResultAudit;
use Vigilant\Lighthouse\Notifications\Conditions\Audit\AuditChangesCondition;
use Vigilant\Lighthouse\Notifications\Conditions\Audit\AuditDecreasesCondition;
use Vigilant\Lighthouse\Notifications\Conditions\Audit\AuditIncreasesCondition;
use Vigilant\Lighthouse\Notifications\NumericAuditChangedNotification;
use Vigilant\Lighthouse\Tests\TestCase;

class AuditChangeConditionsTest extends TestCase
{
    #[Test]
    public function it_checks_audit_increases(): void
    {
        $condition = new AuditIncreasesCondition;

        // 50% increase
        $notification = $this->makeNotification(100, 150, 50);

        $this->assertTrue($condition->applies($notification, null, '>=', 40, null));
        $this->assertTrue($condition->applies($notification, null, '>=', 50, null));
        $this->assertFalse($condition->applies($notification, null, '>=', 60, null));
        $this->assertTrue($condition->applies($notification, null, '>', 40, null));
        $this->assertFalse($condition->applies($notification, null, '>', 50, null));
    }

    #[Test]
    public function it_checks_audit_decreases(): void
    {
        $condition = new AuditDecreasesCondition;

        // 33.33% decrease
        $notification = $this->makeNotification(150, 100, -33.33);

        $this->assertTrue($condition->applies($notification, null, '>=', 30, null));
        $this->assertTrue($condition->applies($notification, null, '>=', 33, null));
        $this->assertFalse($condition->applies($notification, null, '>=', 40, null));
        $this->assertTrue($condition->applies($notification, null, '>', 30, null));
        $this->assertFalse($condition->applies($notification, null, '>', 33.33, null));
    }

    #[Test]
    public function it_checks_audit_changes_either_direction(): void
    {
        $conditionIncrease = new AuditChangesCondition;
        $conditionDecrease = new AuditChangesCondition;

        $notificationIncrease = $this->makeNotification(100, 150, 50);
        $notificationDecrease = $this->makeNotification(150, 100, -33.33);

        $this->assertTrue($conditionIncrease->applies($notificationIncrease, null, '>=', 40, null));
        $this->assertTrue($conditionDecrease->applies($notificationDecrease, null, '>=', 30, null));
        
        $this->assertTrue($conditionIncrease->applies($notificationIncrease, null, '>', 40, null));
        $this->assertFalse($conditionIncrease->applies($notificationIncrease, null, '>', 50, null));
    }

    #[Test]
    public function it_does_not_trigger_increase_on_decrease(): void
    {
        $condition = new AuditIncreasesCondition;
        $notification = $this->makeNotification(150, 100, -33.33);

        $this->assertFalse($condition->applies($notification, null, '>=', 10, null));
    }

    #[Test]
    public function it_does_not_trigger_decrease_on_increase(): void
    {
        $condition = new AuditDecreasesCondition;
        $notification = $this->makeNotification(100, 150, 50);

        $this->assertFalse($condition->applies($notification, null, '>=', 10, null));
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
