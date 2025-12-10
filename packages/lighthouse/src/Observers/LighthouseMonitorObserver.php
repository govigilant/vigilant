<?php

namespace Vigilant\Lighthouse\Observers;

use Vigilant\Lighthouse\Jobs\RunLighthouseJob;
use Vigilant\Lighthouse\Models\LighthouseMonitor;

class LighthouseMonitorObserver
{
    public function created(LighthouseMonitor $monitor): void
    {
        RunLighthouseJob::dispatch($monitor);
    }
}
