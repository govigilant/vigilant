<?php

namespace Vigilant\Uptime;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Vigilant\Uptime\Events\DowntimeEndEvent;
use Vigilant\Uptime\Events\DowntimeStartEvent;
use Vigilant\Uptime\Events\UptimeCheckedEvent;
use Vigilant\Uptime\Listeners\CheckLatencyListener;
use Vigilant\Uptime\Listeners\DowntimeEndNotificationListener;
use Vigilant\Uptime\Listeners\DowntimeStartNotificationListener;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        DowntimeStartEvent::class => [
            DowntimeStartNotificationListener::class,
        ],
        DowntimeEndEvent::class => [
            DowntimeEndNotificationListener::class,
        ],
        UptimeCheckedEvent::class => [
            CheckLatencyListener::class,
        ],
    ];
}
