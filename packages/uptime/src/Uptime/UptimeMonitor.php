<?php

namespace Vigilant\Uptime\Uptime;

use Vigilant\Uptime\Data\UptimeResult;
use Vigilant\Uptime\Models\Monitor;

abstract class UptimeMonitor
{
    abstract public function process(Monitor $monitor): UptimeResult;
}
