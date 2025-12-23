<?php

namespace Vigilant\Uptime\Actions\Outpost;

use Vigilant\Uptime\Actions\FetchGeolocation;
use Vigilant\Uptime\Enums\OutpostStatus;
use Vigilant\Uptime\Models\Outpost;

class RegisterOutpost
{
    public function __construct(
        protected FetchGeolocation $fetchGeolocation,
    ) {}

    public function register(
        string $externalIp,
        string $ip,
        int $port,
        bool $geoipAutomatic = true,
        ?string $country = null,
        ?float $latitude = null,
        ?float $longitude = null,
    ): Outpost {
        if (! $geoipAutomatic) {
            return Outpost::query()->updateOrCreate([
                'ip' => $ip,
                'port' => $port,
            ], [
                'external_ip' => $externalIp,
                'status' => OutpostStatus::Available,
                'country' => $country !== null ? strtoupper($country) : null,
                'latitude' => $latitude !== null ? (float) $latitude : null,
                'longitude' => $longitude !== null ? (float) $longitude : null,
                'geoip_automatic' => false,
                'last_available_at' => now(),
            ]);
        }

        $existingOutpost = Outpost::query()
            ->where('ip', '=', $ip)
            ->where('port', '=', $port)
            ->first();

        if ($existingOutpost && $existingOutpost->country !== null && $existingOutpost->geoip_automatic) {
            $existingOutpost->external_ip = $externalIp;
            $existingOutpost->last_available_at = now();
            $existingOutpost->status = OutpostStatus::Available;
            $existingOutpost->geoip_automatic = true;

            $existingOutpost->save();

            return $existingOutpost;
        }

        $geolocation = $this->fetchGeolocation->fetch($externalIp);

        return Outpost::query()->updateOrCreate([
            'ip' => $ip,
            'port' => $port,
        ], [
            'external_ip' => $externalIp,
            'status' => OutpostStatus::Available,
            'country' => isset($geolocation['country']) ? strtoupper($geolocation['country']) : null,
            'latitude' => $geolocation['latitude'] ?? null,
            'longitude' => $geolocation['longitude'] ?? null,
            'geoip_automatic' => true,
            'last_available_at' => now(),
        ]);
    }
}
