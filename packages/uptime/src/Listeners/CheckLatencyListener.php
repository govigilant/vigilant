<?php

namespace Vigilant\Uptime\Listeners;

use Vigilant\Uptime\Actions\CheckLatency;
use Vigilant\Uptime\Events\UptimeCheckedEvent;

class CheckLatencyListener
{
    public function __construct(protected CheckLatency $checker) {}

    public function handle(UptimeCheckedEvent $event): void
    {
        $this->checker->check($event->monitor);
    }
}
