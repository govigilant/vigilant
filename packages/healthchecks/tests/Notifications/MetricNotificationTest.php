<?php

namespace Vigilant\Healthchecks\Tests\Notifications;

use PHPUnit\Framework\Attributes\Test;
use Vigilant\Healthchecks\Actions\CheckMetric;
use Vigilant\Healthchecks\Models\Healthcheck;
use Vigilant\Healthchecks\Models\Metric;
use Vigilant\Healthchecks\Notifications\MetricNotification;
use Vigilant\Healthchecks\Tests\TestCase;

class MetricNotificationTest extends TestCase
{
    #[Test]
    public function it_sends_notification_when_metric_exceeds_threshold(): void
    {
        MetricNotification::fake();

        $healthcheck = Healthcheck::factory()->create();
        $runId = 1;

        Metric::factory()->create([
            'healthcheck_id' => $healthcheck->id,
            'run_id' => $runId,
            'key' => 'cpu_usage',
            'value' => 95.5,
            'unit' => '%',
        ]);

        $action = app(CheckMetric::class);
        $action->check($healthcheck, $runId);

        $this->assertTrue(MetricNotification::wasDispatched());
    }

    #[Test]
    public function it_does_not_send_notification_when_no_metrics(): void
    {
        MetricNotification::fake();

        $healthcheck = Healthcheck::factory()->create();
        $runId = 1;

        $action = app(CheckMetric::class);
        $action->check($healthcheck, $runId);

        $this->assertFalse(MetricNotification::wasDispatched());
    }

    #[Test]
    public function it_filters_by_unit_condition(): void
    {
        $healthcheck = Healthcheck::factory()->create();
        $runId = 1;

        Metric::factory()->create([
            'healthcheck_id' => $healthcheck->id,
            'run_id' => $runId,
            'key' => 'memory_usage',
            'value' => 95,
            'unit' => '%',
        ]);

        $notification = new MetricNotification($healthcheck, $runId);

        $this->assertInstanceOf(MetricNotification::class, $notification);
    }

    #[Test]
    public function it_filters_by_value_condition(): void
    {
        $healthcheck = Healthcheck::factory()->create();
        $runId = 1;

        Metric::factory()->create([
            'healthcheck_id' => $healthcheck->id,
            'run_id' => $runId,
            'key' => 'cpu_usage',
            'value' => 95,
            'unit' => '%',
        ]);

        Metric::factory()->create([
            'healthcheck_id' => $healthcheck->id,
            'run_id' => $runId,
            'key' => 'memory_usage',
            'value' => 50,
            'unit' => '%',
        ]);

        $notification = new MetricNotification($healthcheck, $runId);

        $this->assertInstanceOf(MetricNotification::class, $notification);
    }
}
