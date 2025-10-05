<?php

namespace Vigilant\Uptime\Actions\Outpost;

use Illuminate\Support\Facades\Http;
use Vigilant\Uptime\Enums\OutpostStatus;
use Vigilant\Uptime\Models\Outpost;

class RegisterOutpost
{
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

        $ipDetails = Http::get('https://free.freeipapi.com/api/json/'.$externalIp)->throw()->json();

        return Outpost::query()->updateOrCreate([
            'ip' => $ip,
            'port' => $port,
        ], [
            'external_ip' => $externalIp,
            'status' => OutpostStatus::Available,
            'country' => $ipDetails['countryCode'] ?? null,
            'latitude' => $ipDetails['latitude'] ?? null,
            'longitude' => $ipDetails['longitude'] ?? null,
            'last_available_at' => now(),
        ]);
    }
}
