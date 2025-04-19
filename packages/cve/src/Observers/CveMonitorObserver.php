<?php

namespace Vigilant\Cve\Observers;

use Vigilant\Cve\Jobs\MatchExistingCvesJob;
use Vigilant\Cve\Models\CveMonitor;

class CveMonitorObserver
{
    public function created(CveMonitor $monitor): void
    {
        MatchExistingCvesJob::dispatch($monitor);
    }
}
