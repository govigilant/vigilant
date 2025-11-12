<?php

namespace Vigilant\Healthchecks\Observers;

use Illuminate\Support\Str;
use Vigilant\Healthchecks\Models\Healthcheck;

class HealthcheckObserver
{
    public function creating(Healthcheck $healthcheck): void
    {
        if (empty($healthcheck->token)) {
            $healthcheck->token = Str::random(32);
        }
    }
}
