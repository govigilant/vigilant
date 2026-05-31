<?php

namespace Vigilant\Core\Http;

use BlueLibraries\Dns\Records\RecordTypes;
use BlueLibraries\Dns\Records\Types\A;
use BlueLibraries\Dns\Records\Types\AAAA;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Vigilant\Core\Http\Exceptions\SsrfException;
use Vigilant\Dns\Client\DnsClient;

class SsrfGuard
{
    public function __construct(protected DnsClient $dnsClient) {}

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

        throw_if(
            $parts === false || ! isset($parts['scheme'], $parts['host']),
            SsrfException::invalidUrl($url),
        );

        $scheme = strtolower((string) $parts['scheme']);
        $host = $this->normalizeHost((string) $parts['host']);
        $port = isset($parts['port']) ? (int) $parts['port'] : ($scheme === 'https' ? 443 : 80);

        $ips = $this->resolveSafeIps($url);

        if ($ips === []) {
            return Http::createPendingRequest();
        }

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

        throw_if(
            $parts === false || ! isset($parts['scheme'], $parts['host']),
            SsrfException::invalidUrl($url),
        );

        $scheme = strtolower((string) $parts['scheme']);

        throw_unless(
            in_array($scheme, ['http', 'https'], true),
            SsrfException::disallowedScheme($scheme),
        );

        throw_if(
            isset($parts['user']) || isset($parts['pass']),
            SsrfException::userInfoForbidden(),
        );

        $host = $this->normalizeHost((string) $parts['host']);

        throw_if($host === '', SsrfException::invalidUrl($url));

        if ($this->isAllowedHost($host)) {
            // Trusted host: skip DNS resolution and IP checks. curl will
            // resolve via the OS resolver (which may know internal DNS).
            return [];
        }

        $ips = $this->resolveIps($host);

        throw_if($ips === [], SsrfException::unresolvable($host));

        foreach ($ips as $ip) {
            throw_unless($this->isPublicIp($ip), SsrfException::blockedIp($ip));
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

        foreach ($this->dnsClient->get($host, RecordTypes::A) as $record) {
            if ($record instanceof A && ($ip = $record->getIp()) !== null) {
                $ips[] = $ip;
            }
        }

        foreach ($this->dnsClient->get($host, RecordTypes::AAAA) as $record) {
            if ($record instanceof AAAA && ($ip = $record->getIPV6()) !== null) {
                $ips[] = $ip;
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

        // Unwrap IPv4-mapped (::ffff:a.b.c.d) and IPv4-compatible (::a.b.c.d)
        // IPv6 addresses to their dotted-quad form regardless of textual
        // representation (e.g. ::ffff:7f00:1 == ::ffff:127.0.0.1).
        $unwrapped = $this->unwrapIpv4InIpv6($ip);

        if ($unwrapped !== null) {
            return $this->isPublicIp($unwrapped);
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

    /**
     * If $ip is an IPv6 address embedding an IPv4 address (mapped ::ffff:0:0/96
     * or compatible ::/96), return the dotted-quad form. Otherwise null.
     */
    protected function unwrapIpv4InIpv6(string $ip): ?string
    {
        $packed = @inet_pton($ip);

        if ($packed === false || strlen($packed) !== 16) {
            return null;
        }

        // Bytes 0-9 must be zero for either mapped or compatible forms.
        if (substr($packed, 0, 10) !== str_repeat("\0", 10)) {
            return null;
        }

        $prefix = substr($packed, 10, 2);

        // ::ffff:a.b.c.d (IPv4-mapped) or ::a.b.c.d (IPv4-compatible).
        if ($prefix !== "\xff\xff" && $prefix !== "\0\0") {
            return null;
        }

        $ipv4 = @inet_ntop(substr($packed, 12, 4));

        return is_string($ipv4) ? $ipv4 : null;
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
