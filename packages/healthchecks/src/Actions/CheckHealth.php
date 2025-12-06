<?php

namespace Vigilant\Healthchecks\Actions;

use Exception;
use Vigilant\Healthchecks\Enums\Status;
use Vigilant\Healthchecks\Jobs\CheckResultJob;
use Vigilant\Healthchecks\Models\Healthcheck;

class CheckHealth
{
    public function check(Healthcheck $healthcheck): void
    {
        $runId = null;

        try {
            $checker = $healthcheck->type->checker();

            $runId = $checker->check($healthcheck);
        } catch (Exception $e) {
            logger()->error('Healthcheck failed for Healthcheck ID '.$healthcheck->id.': '.$e->getMessage());

            if (app()->isLocal()) {
                throw $e;
            }

            $healthcheck->update([
                'status' => Status::Unhealthy,
            ]);
        }

        $healthcheck->update([
            'next_check_at' => now()->addSeconds($healthcheck->interval),
            'last_check_at' => now(),
        ]);

        if ($runId !== null) {
            CheckResultJob::dispatch($healthcheck, $runId);
        }
    }
}
