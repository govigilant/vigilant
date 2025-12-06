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
    public function it_notifies_when_unhealthy(): void
    {
        Bus::fake();
        $this->fakeNotification(HealthCheckFailedNotification::class);

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

        $this->assertNotificationDispatched(
            HealthCheckFailedNotification::class,
            function (HealthCheckFailedNotification $notification) use ($healthcheck): bool {
                return $notification->healthcheck->is($healthcheck)
                    && $notification->runId === 42;
            }
        );

        $refreshedHealthcheck = $healthcheck->fresh();
        $this->assertInstanceOf(Healthcheck::class, $refreshedHealthcheck);
        $this->assertSame(Status::Unhealthy, $refreshedHealthcheck->status);

        Bus::assertDispatched(CheckMetricJob::class, function (CheckMetricJob $job) use ($healthcheck) {
            return $job->healthcheck->is($healthcheck)
                && $job->runId === 42;
        });
    }

    #[Test]
    public function it_sets_status_to_warning_when_results_have_warnings(): void
    {
        Bus::fake();
        $this->fakeNotification(HealthCheckFailedNotification::class);

        $healthcheck = Healthcheck::query()->create([
            'domain' => 'example.com',
            'type' => Type::Laravel,
            'interval' => 5,
            'token' => 'result-warning-token',
        ]);

        Result::query()->create([
            'healthcheck_id' => $healthcheck->id,
            'key' => 'uptime',
            'status' => Status::Warning,
            'message' => 'Slow response',
        ]);

        /** @var CheckResult $action */
        $action = app(CheckResult::class);
        $action->check($healthcheck, 7);

        $this->assertNotificationDispatched(
            HealthCheckFailedNotification::class,
            function (HealthCheckFailedNotification $notification) use ($healthcheck): bool {
                return $notification->healthcheck->is($healthcheck)
                    && $notification->runId === 7;
            }
        );

        $refreshedHealthcheck = $healthcheck->fresh();
        $this->assertInstanceOf(Healthcheck::class, $refreshedHealthcheck);
        $this->assertSame(Status::Warning, $refreshedHealthcheck->status);

        Bus::assertDispatched(CheckMetricJob::class, function (CheckMetricJob $job) use ($healthcheck) {
            return $job->healthcheck->is($healthcheck)
                && $job->runId === 7;
        });
    }

    #[Test]
    public function it_does_not_notify_when_all_results_are_healthy(): void
    {
        Bus::fake();
        $this->fakeNotification(HealthCheckFailedNotification::class);

        $healthcheck = Healthcheck::query()->create([
            'domain' => 'example.com',
            'type' => Type::Laravel,
            'interval' => 5,
            'token' => 'result-healthy-token',
        ]);

        Result::query()->create([
            'healthcheck_id' => $healthcheck->id,
            'key' => 'uptime',
            'status' => Status::Healthy,
        ]);

        /** @var CheckResult $action */
        $action = app(CheckResult::class);
        $action->check($healthcheck, 99);

        $this->assertNotificationNotDispatched(HealthCheckFailedNotification::class);
        $refreshedHealthcheck = $healthcheck->fresh();
        $this->assertInstanceOf(Healthcheck::class, $refreshedHealthcheck);
        $this->assertSame(Status::Healthy, $refreshedHealthcheck->status);

        Bus::assertDispatched(CheckMetricJob::class, function (CheckMetricJob $job) use ($healthcheck) {
            return $job->healthcheck->is($healthcheck)
                && $job->runId === 99;
        });
    }

    /**
     * @param  class-string<\Vigilant\Notifications\Notifications\Notification>  $notificationClass
     */
    private function fakeNotification(string $notificationClass): void
    {
        $notificationClass::fake();

        $this->resetNotificationFakes($notificationClass);
    }

    /**
     * @param  class-string<\Vigilant\Notifications\Notifications\Notification>  $notificationClass
     */
    private function assertNotificationNotDispatched(string $notificationClass): void
    {
        $this->assertEmpty(
            $this->notificationDispatches($notificationClass),
            "Did not expect {$notificationClass} to be dispatched."
        );
    }

    /**
     * @param  class-string<\Vigilant\Notifications\Notifications\Notification>  $notificationClass
     */
    private function assertNotificationDispatched(string $notificationClass, callable $callback): void
    {
        $dispatches = $this->notificationDispatches($notificationClass);

        $this->assertNotEmpty(
            $dispatches,
            "Expected {$notificationClass} to be dispatched."
        );

        $matched = false;

        foreach ($dispatches as $dispatch) {
            if ($callback($dispatch)) {
                $matched = true;
                break;
            }
        }

        $this->assertTrue(
            $matched,
            "No dispatched {$notificationClass} matched the provided conditions."
        );
    }

    /**
     * @param  class-string<\Vigilant\Notifications\Notifications\Notification>  $notificationClass
     * @return array<int, \Vigilant\Notifications\Notifications\Notification>
     */
    private function notificationDispatches(string $notificationClass): array
    {
        $property = new \ReflectionProperty($notificationClass, 'fakeDispatches');
        $property->setAccessible(true);

        /** @var array<int, \Vigilant\Notifications\Notifications\Notification> $dispatches */
        $dispatches = $property->getValue();

        return $dispatches;
    }

    /**
     * @param  class-string<\Vigilant\Notifications\Notifications\Notification>  $notificationClass
     */
    private function resetNotificationFakes(string $notificationClass): void
    {
        $property = new \ReflectionProperty($notificationClass, 'fakeDispatches');
        $property->setAccessible(true);
        $property->setValue(null, []);
    }
}
