<?php

namespace Vigilant\Healthchecks\Actions;

use Exception;
use Vigilant\Healthchecks\Jobs\CheckResultJob;
use Vigilant\Healthchecks\Models\Healthcheck;

class CheckHealth
{
    public function check(Healthcheck $healthcheck): void
    {
        try {
            $checker = $healthcheck->type->checker();

            $runId = $checker->check($healthcheck);
        } catch (Exception $e) {
            $result = null;

            logger()->error('Healthcheck failed for Healthcheck ID '.$healthcheck->id.': '.$e->getMessage());

            if (app()->isLocal()) {
                throw $e;
            }
        }

        $healthcheck->update([
            'next_check_at' => now()->addSeconds($healthcheck->interval),
            'last_check_at' => now(),
            'status' => $result->status ?? null,
        ]);

        if (isset($runId)) {
            CheckResultJob::dispatch($healthcheck, $runId);
        }
    }
}
