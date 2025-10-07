<?php

namespace Vigilant\Uptime\Actions;

use Illuminate\Support\Facades\Http;
use Vigilant\Dns\Actions\ResolveRecord;
use Vigilant\Dns\Enums\Type;
use Vigilant\Dns\Models\DnsMonitor;

class FetchGeolocation
{
    public function __construct(protected ResolveRecord $resolveRecord) {}

    public function fetch(string $target): ?array
    {
        $host = $this->extractHost($target);

        if ($host === null) {
            return null;
        }

        // If host is a domain, resolve it to an IP address
        if (! $this->isIpAddress($host)) {
            $host = $this->resolveToIp($host);

            if ($host === null) {
                return null;
            }

            $host = str($host)->before(',')->toString();
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

    protected function isIpAddress(string $host): bool
    {
        return filter_var($host, FILTER_VALIDATE_IP) !== false;
    }

    protected function resolveToIp(string $domain): ?string
    {
        $dnsMonitor = DnsMonitor::where('record', $domain)
            ->whereIn('type', [Type::A, Type::AAAA])
            ->where('enabled', '=', true)
            ->first();

        if ($dnsMonitor && $dnsMonitor->value) {
            return $dnsMonitor->value;
        }

        try {
            $ip = $this->resolveRecord->resolve(Type::A, $domain);

            if ($ip !== null) {
                return $ip;
            }

            return $this->resolveRecord->resolve(Type::AAAA, $domain);
        } catch (\Exception $e) {
            logger()->warning('Failed to resolve domain to IP for '.$domain, [
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }
}
