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

        foreach ($results as $result) {
            if ($result->status !== Status::Healthy) {
                HealthCheckFailedNotification::notify($healthcheck, $runId);
                break;
            }
        }
    }
}
