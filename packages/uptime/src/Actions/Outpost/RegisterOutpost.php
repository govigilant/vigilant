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

    public function register(string $externalIp, string $ip, int $port): Outpost
    {
        $existingOutpost = Outpost::query()
            ->where('ip', '=', $ip)
            ->where('port', '=', $port)
            ->first();

        if ($existingOutpost && $existingOutpost->country !== null) {
            $existingOutpost->external_ip = $externalIp;
            $existingOutpost->last_available_at = now();
            $existingOutpost->status = OutpostStatus::Available;

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
            'country' => $geolocation['country'] ?? null,
            'latitude' => $geolocation['latitude'] ?? null,
            'longitude' => $geolocation['longitude'] ?? null,
            'last_available_at' => now(),
        ]);
    }
}
