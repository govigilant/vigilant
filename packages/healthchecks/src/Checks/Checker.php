<?php

namespace Vigilant\Healthchecks\Checks;

use Vigilant\Healthchecks\Models\Healthcheck;

abstract class Checker
{
    /** @return int runId */
    abstract public function check(Healthcheck $healthcheck): int;
}
