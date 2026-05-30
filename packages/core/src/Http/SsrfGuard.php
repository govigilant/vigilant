<?php

namespace Vigilant\Core\Http;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Vigilant\Core\Http\Exceptions\SsrfException;

class SsrfGuard
{
    /**
     * Hostnames that should never be contacted regardless of resolved IP.
     * Covers known cloud metadata service hostnames.
     */
    protected const BLOCKED_HOSTNAMES = [
        'metadata.google.internal',
        'metadata.goog',
        'metadata.azure.com',
        'instance-data',
        'instance-data.ec2.internal',
    ];

    /**
     * Asserts the URL is safe to fetch. Throws SsrfException on violation.
     */
    public function assertSafeUrl(string $url): void
    {
        $this->resolveSafeIps($url);
    }

    /**
     * Returns a PendingRequest bound to the URL with the resolved IPs pinned
     * via CURLOPT_RESOLVE to prevent DNS rebinding between validation and
     * the actual request.
     */
    public function request(string $url): PendingRequest
    {
        $parts = parse_url($url);

        if ($parts === false || ! isset($parts['scheme'], $parts['host'])) {
            throw SsrfException::invalidUrl($url);
        }

        $scheme = strtolower((string) $parts['scheme']);
        $host = $this->normalizeHost((string) $parts['host']);
        $port = isset($parts['port']) ? (int) $parts['port'] : ($scheme === 'https' ? 443 : 80);

        $ips = $this->resolveSafeIps($url);

        $resolveEntries = array_map(
            fn (string $ip): string => sprintf('%s:%d:%s', $host, $port, $ip),
            $ips,
        );

        return Http::withOptions([
            'curl' => [
                CURLOPT_RESOLVE => $resolveEntries,
            ],
        ]);
    }

    /**
     * Validates the URL and returns the list of resolved IP addresses.
     *
     * @return array<int, string>
     */
    protected function resolveSafeIps(string $url): array
    {
        $parts = parse_url($url);

        if ($parts === false || ! isset($parts['scheme'], $parts['host'])) {
            throw SsrfException::invalidUrl($url);
        }

        $scheme = strtolower((string) $parts['scheme']);

        if (! in_array($scheme, ['http', 'https'], true)) {
            throw SsrfException::disallowedScheme($scheme);
        }

        if (isset($parts['user']) || isset($parts['pass'])) {
            throw SsrfException::userInfoForbidden();
        }

        $host = $this->normalizeHost((string) $parts['host']);

        if ($host === '') {
            throw SsrfException::invalidUrl($url);
        }

        if ($this->isAllowedHost($host)) {
            $ips = $this->resolveIps($host);

            return $ips === [] ? [$host] : $ips;
        }

        foreach (self::BLOCKED_HOSTNAMES as $blocked) {
            if ($host === $blocked || str_ends_with($host, '.'.$blocked)) {
                throw SsrfException::blockedHost($host);
            }
        }

        $ips = $this->resolveIps($host);

        if ($ips === []) {
            throw SsrfException::unresolvable($host);
        }

        foreach ($ips as $ip) {
            if (! $this->isPublicIp($ip)) {
                throw SsrfException::blockedIp($ip);
            }
        }

        return $ips;
    }

    /**
     * @return array<int, string>
     */
    protected function resolveIps(string $host): array
    {
        // Bracketed IPv6 literal: [::1]
        if (str_starts_with($host, '[') && str_ends_with($host, ']')) {
            $stripped = substr($host, 1, -1);

            if (filter_var($stripped, FILTER_VALIDATE_IP) !== false) {
                return [$stripped];
            }

            return [];
        }

        // Literal IPv4/IPv6
        if (filter_var($host, FILTER_VALIDATE_IP) !== false) {
            return [$host];
        }

        // Detect numerically-encoded IPv4 (decimal/hex/octal) to prevent
        // bypasses like http://2130706433 (== 127.0.0.1).
        $packed = $this->packNumericIpv4($host);

        if ($packed !== null) {
            return [$packed];
        }

        $ips = [];

        $aRecords = @dns_get_record($host, DNS_A);

        if (is_array($aRecords)) {
            foreach ($aRecords as $record) {
                if (isset($record['ip']) && is_string($record['ip'])) {
                    $ips[] = $record['ip'];
                }
            }
        }

        $aaaaRecords = @dns_get_record($host, DNS_AAAA);

        if (is_array($aaaaRecords)) {
            foreach ($aaaaRecords as $record) {
                if (isset($record['ipv6']) && is_string($record['ipv6'])) {
                    $ips[] = $record['ipv6'];
                }
            }
        }

        if ($ips === []) {
            $fallback = @gethostbynamel($host);

            if (is_array($fallback)) {
                $ips = $fallback;
            }
        }

        return array_values(array_unique($ips));
    }

    /**
     * Convert numerically-encoded IPv4 strings (decimal, hex, octal, or
     * dotted variants) to dotted-quad. Returns null if the host is not such a
     * representation.
     */
    protected function packNumericIpv4(string $host): ?string
    {
        // Pure decimal integer host (e.g. "2130706433")
        if (preg_match('/^\d+$/', $host) === 1) {
            $value = (int) $host;

            if ($value < 0 || $value > 0xFFFFFFFF) {
                return null;
            }

            return long2ip($value);
        }

        // Hexadecimal (e.g. "0x7f000001")
        if (preg_match('/^0x[0-9a-f]+$/i', $host) === 1) {
            $value = hexdec($host);

            if (! is_int($value) || $value < 0 || $value > 0xFFFFFFFF) {
                return null;
            }

            return long2ip($value);
        }

        return null;
    }

    protected function isPublicIp(string $ip): bool
    {
        if (filter_var($ip, FILTER_VALIDATE_IP) === false) {
            return false;
        }

        // Unwrap IPv4-mapped IPv6 (::ffff:127.0.0.1) and revalidate.
        if (preg_match('/^::ffff:(\d+\.\d+\.\d+\.\d+)$/i', $ip, $matches) === 1) {
            return $this->isPublicIp($matches[1]);
        }

        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false) {
            return false;
        }

        // FILTER_FLAG_NO_RES_RANGE does not exclude carrier-grade NAT.
        if ($this->ipInCidr($ip, '100.64.0.0/10')) {
            return false;
        }

        // Explicit defence-in-depth for cloud metadata addresses.
        if ($ip === '169.254.169.254' || $ip === 'fd00:ec2::254') {
            return false;
        }

        foreach ($this->extraBlockedCidrs() as $cidr) {
            if ($this->ipInCidr($ip, $cidr)) {
                return false;
            }
        }

        return true;
    }

    protected function ipInCidr(string $ip, string $cidr): bool
    {
        if (! str_contains($cidr, '/')) {
            return $ip === $cidr;
        }

        [$subnet, $maskLength] = explode('/', $cidr, 2);
        $maskLength = (int) $maskLength;

        $ipPacked = @inet_pton($ip);
        $subnetPacked = @inet_pton($subnet);

        if ($ipPacked === false || $subnetPacked === false) {
            return false;
        }

        if (strlen($ipPacked) !== strlen($subnetPacked)) {
            return false;
        }

        $bytes = intdiv($maskLength, 8);
        $bits = $maskLength % 8;

        if ($bytes > 0 && substr($ipPacked, 0, $bytes) !== substr($subnetPacked, 0, $bytes)) {
            return false;
        }

        if ($bits === 0) {
            return true;
        }

        $mask = chr((0xFF << (8 - $bits)) & 0xFF);

        return (ord($ipPacked[$bytes]) & ord($mask)) === (ord($subnetPacked[$bytes]) & ord($mask));
    }

    protected function normalizeHost(string $host): string
    {
        $host = strtolower(trim($host));

        // Strip trailing dot from FQDN.
        if (str_ends_with($host, '.') && ! str_ends_with($host, '..')) {
            $host = rtrim($host, '.');
        }

        return $host;
    }

    protected function isAllowedHost(string $host): bool
    {
        foreach ($this->allowedHosts() as $allowed) {
            if ($host === $allowed) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return array<int, string>
     */
    protected function allowedHosts(): array
    {
        $hosts = config('core.ssrf.allowed_hosts', []);

        if (! is_array($hosts)) {
            return [];
        }

        return array_values(array_filter(array_map(
            fn ($host): string => is_string($host) ? $this->normalizeHost($host) : '',
            $hosts,
        )));
    }

    /**
     * @return array<int, string>
     */
    protected function extraBlockedCidrs(): array
    {
        $cidrs = config('core.ssrf.extra_blocked_cidrs', []);

        if (! is_array($cidrs)) {
            return [];
        }

        return array_values(array_filter(array_map(
            fn ($cidr): string => is_string($cidr) ? $cidr : '',
            $cidrs,
        )));
    }
}
