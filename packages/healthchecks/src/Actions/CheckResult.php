<?php

namespace Vigilant\Healthchecks\Actions;

use Vigilant\Healthchecks\Enums\Status;
use Vigilant\Healthchecks\Models\Healthcheck;
use Vigilant\Healthchecks\Notifications\HealthCheckFailedNotification;

class CheckResult
{
    public function check(Healthcheck $healthcheck, int $runId): void
    {
        /** @var \Illuminate\Database\Eloquent\Collection<int, \Vigilant\Healthchecks\Models\Result> $results */
        $results = $healthcheck->results()
            ->where('run_id', $runId)
            ->get();

        $overallStatus = Status::Healthy;
        $hasUnhealthy = false;
        $hasWarning = false;

        foreach ($results as $result) {
            if ($result->status === Status::Unhealthy) {
                $hasUnhealthy = true;
                break;
            }
            if ($result->status === Status::Warning) {
                $hasWarning = true;
            }
        }

        if ($hasUnhealthy) {
            $overallStatus = Status::Unhealthy;
            HealthCheckFailedNotification::notify($healthcheck, $runId);
        } elseif ($hasWarning) {
            $overallStatus = Status::Warning;
        }

        $healthcheck->update(['status' => $overallStatus]);
    }
}
