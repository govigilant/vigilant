<?php

namespace Vigilant\Healthchecks\Tests\Notifications;

use PHPUnit\Framework\Attributes\Test;
use Vigilant\Healthchecks\Actions\CheckMetric;
use Vigilant\Healthchecks\Models\Healthcheck;
use Vigilant\Healthchecks\Models\Metric;
use Vigilant\Healthchecks\Notifications\DiskUsageNotification;
use Vigilant\Healthchecks\Tests\TestCase;

class DiskUsageNotificationTest extends TestCase
{
    #[Test]
    public function it_sends_notification_when_disk_will_be_full_soon(): void
    {
        DiskUsageNotification::fake();

        $healthcheck = Healthcheck::factory()->create();
        $baseTime = now();

        // Create metrics showing disk usage going from 70% to 90% in 2 hours
        // Velocity = 10% per hour, will reach 100% in 1 hour
        Metric::factory()->create([
            'healthcheck_id' => $healthcheck->id,
            'run_id' => 1,
            'key' => 'disk_usage',
            'value' => 70,
            'unit' => '%',
            'created_at' => $baseTime->copy()->subHours(2),
        ]);

        Metric::factory()->create([
            'healthcheck_id' => $healthcheck->id,
            'run_id' => 2,
            'key' => 'disk_usage',
            'value' => 80,
            'unit' => '%',
            'created_at' => $baseTime->copy()->subHour(),
        ]);

        Metric::factory()->create([
            'healthcheck_id' => $healthcheck->id,
            'run_id' => 3,
            'key' => 'disk_usage',
            'value' => 90,
            'unit' => '%',
            'created_at' => $baseTime,
        ]);

        $action = app(CheckMetric::class);
        $action->check($healthcheck, 3);

        $this->assertTrue(DiskUsageNotification::wasDispatched());
    }

    #[Test]
    public function it_calculates_hours_until_full_correctly(): void
    {
        $healthcheck = Healthcheck::factory()->create();
        $baseTime = now();

        // 80% to 95% in 3 hours = 5% per hour velocity
        // Remaining space = 5%, will be full in 1 hour
        Metric::factory()->create([
            'healthcheck_id' => $healthcheck->id,
            'run_id' => 1,
            'key' => 'disk_usage',
            'value' => 80,
            'unit' => '%',
            'created_at' => $baseTime->copy()->subHours(3),
        ]);

        Metric::factory()->create([
            'healthcheck_id' => $healthcheck->id,
            'run_id' => 2,
            'key' => 'disk_usage',
            'value' => 95,
            'unit' => '%',
            'created_at' => $baseTime,
        ]);

        $currentUsage = 95;
        $velocity = (95 - 80) / 3; // 5% per hour
        $remainingSpace = 100 - 95; // 5%
        $hoursUntilFull = $remainingSpace / $velocity; // 1 hour

        $notification = new DiskUsageNotification(
            $healthcheck,
            2,
            $currentUsage,
            $velocity,
            $hoursUntilFull,
            now()->addHours($hoursUntilFull)->toDateTimeString()
        );

        $this->assertEquals(95, $notification->currentUsage);
        $this->assertEquals(5, $notification->velocity);
        $this->assertEquals(1, $notification->hoursUntilFull);
    }

    #[Test]
    public function it_does_not_send_notification_when_disk_usage_stable(): void
    {
        DiskUsageNotification::fake();

        $healthcheck = Healthcheck::factory()->create();
        $baseTime = now();

        Metric::factory()->create([
            'healthcheck_id' => $healthcheck->id,
            'run_id' => 1,
            'key' => 'disk_usage',
            'value' => 50,
            'unit' => '%',
            'created_at' => $baseTime->copy()->subHours(2),
        ]);

        Metric::factory()->create([
            'healthcheck_id' => $healthcheck->id,
            'run_id' => 2,
            'key' => 'disk_usage',
            'value' => 50,
            'unit' => '%',
            'created_at' => $baseTime,
        ]);

        $action = app(CheckMetric::class);
        $action->check($healthcheck, 2);

        $this->assertFalse(DiskUsageNotification::wasDispatched());
    }

    #[Test]
    public function it_does_not_send_notification_when_disk_usage_decreasing(): void
    {
        DiskUsageNotification::fake();

        $healthcheck = Healthcheck::factory()->create();
        $baseTime = now();

        Metric::factory()->create([
            'healthcheck_id' => $healthcheck->id,
            'run_id' => 1,
            'key' => 'disk_usage',
            'value' => 90,
            'unit' => '%',
            'created_at' => $baseTime->copy()->subHours(2),
        ]);

        Metric::factory()->create([
            'healthcheck_id' => $healthcheck->id,
            'run_id' => 2,
            'key' => 'disk_usage',
            'value' => 70,
            'unit' => '%',
            'created_at' => $baseTime,
        ]);

        $action = app(CheckMetric::class);
        $action->check($healthcheck, 2);

        $this->assertFalse(DiskUsageNotification::wasDispatched());
    }

    #[Test]
    public function it_only_checks_disk_usage_metrics(): void
    {
        DiskUsageNotification::fake();

        $healthcheck = Healthcheck::factory()->create();
        $baseTime = now();

        // Create other metrics that should be ignored
        Metric::factory()->create([
            'healthcheck_id' => $healthcheck->id,
            'run_id' => 1,
            'key' => 'cpu_usage',
            'value' => 90,
            'unit' => '%',
            'created_at' => $baseTime,
        ]);

        $action = app(CheckMetric::class);
        $action->check($healthcheck, 1);

        $this->assertFalse(DiskUsageNotification::wasDispatched());
    }
}
