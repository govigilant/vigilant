<?php

namespace Vigilant\Dns\Actions;

use Illuminate\Support\Facades\Http;
use Vigilant\Dns\Models\DnsMonitor;

class ResolveGeoIp
{
    public function resolve(DnsMonitor $monitor): void
    {
        $response = Http::get('http://ip-api.com/json/'.$monitor->value);

        if (! $response->ok() || $response->json('status') !== 'success') {
            return;
        }

        $geoip = $response->json();

        $monitor->update([
            'geoip' => [
                'country_code' => $geoip['countryCode'],
                'country_name' => $geoip['country'],
                'region_code' => $geoip['region'],
                'region_name' => $geoip['regionName'],
                'city' => $geoip['city'],
                'isp' => $geoip['isp'],
                'org' => $geoip['org'],
                'as' => $geoip['as'],
            ],
        ]);
    }
}
