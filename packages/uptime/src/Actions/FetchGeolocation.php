<?php

namespace Vigilant\Uptime\Actions;

use Illuminate\Support\Facades\Http;

class FetchGeolocation
{
    public function fetch(string $target): ?array
    {
        $host = $this->extractHost($target);

        if ($host === null) {
            return null;
        }

        try {
            $response = Http::timeout(10)
                ->get('https://free.freeipapi.com/api/json/'.$host);

            if (! $response->successful()) {
                return null;
            }

            $data = $response->json();

            return [
                'country' => $data['countryCode'] ?? null,
                'latitude' => $data['latitude'] ?? null,
                'longitude' => $data['longitude'] ?? null,
            ];
        } catch (\Exception $e) {
            logger()->warning('Failed to fetch geolocation for '.$host, [
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    protected function extractHost(string $target): ?string
    {
        // If it's a URL, parse the hostname
        if (str_starts_with($target, 'http://') || str_starts_with($target, 'https://')) {
            $parsed = parse_url($target);

            return $parsed['host'] ?? null;
        }

        // If it's in format host:port, extract the host part
        if (str_contains($target, ':')) {
            $parts = explode(':', $target);

            return $parts[0];
        }

        // Otherwise, assume it's already a hostname or IP
        return $target;
    }
}
