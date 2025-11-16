<?php

namespace Vigilant\Healthchecks\Actions;

use Ramsey\Collection\Collection;
use Vigilant\Healthchecks\Enums\Status;
use Vigilant\Healthchecks\Jobs\CheckMetricJob;
use Vigilant\Healthchecks\Models\Healthcheck;
use Vigilant\Healthchecks\Notifications\HealthCheckFailedNotification;

class CheckResult
{
    public function check(Healthcheck $healthcheck, int $runId): void
    {
        /** @var Collection<int, Result> $results */
        $results = $healthcheck->results()
            ->where('run_id', $runId)
            ->get();

        $overallStatus = Status::Healthy;
        $healthy = false;
        $hasWarning = false;

        foreach ($results as $result) {
            if ($result->status === Status::Unhealthy) {
                $healthy = true;
                break;
            }
            if ($result->status === Status::Warning) {
                $hasWarning = true;
            }
        }

        if ($healthy) {
            $overallStatus = Status::Unhealthy;
        } elseif ($hasWarning) {
            $overallStatus = Status::Warning;
        }

        if ($healthy || $hasWarning) {
            HealthCheckFailedNotification::notify($healthcheck, $runId);
        }

        $healthcheck->update(['status' => $overallStatus]);

        CheckMetricJob::dispatch($healthcheck, $runId);
    }
}
