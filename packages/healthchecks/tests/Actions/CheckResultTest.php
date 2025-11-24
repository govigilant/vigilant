<?php

namespace Vigilant\Healthchecks\Tests\Actions;

use Illuminate\Support\Facades\Bus;
use PHPUnit\Framework\Attributes\Test;
use Vigilant\Healthchecks\Actions\CheckResult;
use Vigilant\Healthchecks\Enums\Status;
use Vigilant\Healthchecks\Enums\Type;
use Vigilant\Healthchecks\Jobs\CheckMetricJob;
use Vigilant\Healthchecks\Models\Healthcheck;
use Vigilant\Healthchecks\Models\Result;
use Vigilant\Healthchecks\Notifications\HealthCheckFailedNotification;
use Vigilant\Healthchecks\Tests\TestCase;

class CheckResultTest extends TestCase
{
    #[Test]
    public function it_notifies_wen_unhealthy(): void
    {
        Bus::fake();
        HealthCheckFailedNotification::fake();

        $healthcheck = Healthcheck::query()->create([
            'domain' => 'example.com',
            'type' => Type::Laravel,
            'interval' => 5,
            'token' => 'result-test-token',
        ]);

        Result::query()->create([
            'healthcheck_id' => $healthcheck->id,
            'key' => 'uptime',
            'status' => Status::Unhealthy,
            'message' => 'Service unavailable',
        ]);

        /** @var CheckResult $action */
        $action = app(CheckResult::class);
        $action->check($healthcheck, 42);

        $this->assertTrue(HealthCheckFailedNotification::wasDispatched(function ($notification) use ($healthcheck) {
            if (! $notification instanceof HealthCheckFailedNotification) {
                return true;
            }

            return $notification->healthcheck->is($healthcheck)
                && $notification->runId === 42;
        }));

        Bus::assertDispatched(CheckMetricJob::class, function (CheckMetricJob $job) use ($healthcheck) {
            return $job->healthcheck->is($healthcheck)
                && $job->runId === 42;
        });
    }
}
