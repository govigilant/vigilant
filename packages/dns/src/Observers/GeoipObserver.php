<?php

namespace Vigilant\Dns\Observers;

use Vigilant\Dns\Enums\Type;
use Vigilant\Dns\Jobs\ResolveGeoIpJob;
use Vigilant\Dns\Models\DnsMonitor;

class GeoipObserver
{
    public function updating(DnsMonitor $monitor): void
    {
        if ($monitor->isDirty('value') && in_array($monitor->type, Type::geoIpableTypes())) {
            ResolveGeoIpJob::dispatch($monitor)->delay(now()->addSeconds(5));
        }
    }

    public function created(DnsMonitor $monitor): void
    {
        if (in_array($monitor->type, Type::geoIpableTypes())) {
            ResolveGeoIpJob::dispatch($monitor);
        }
    }
}
