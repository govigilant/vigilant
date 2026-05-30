<?php

namespace Vigilant\Core\Tests\Http;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Vigilant\Core\Http\Exceptions\SsrfException;
use Vigilant\Core\Http\SsrfGuard;
use Vigilant\Core\Tests\TestCase;

class SsrfGuardTest extends TestCase
{
    #[Test]
    #[DataProvider('blockedUrls')]
    public function it_blocks_disallowed_urls(string $url): void
    {
        $guard = app(SsrfGuard::class);

        $this->expectException(SsrfException::class);

        $guard->assertSafeUrl($url);
    }

    public static function blockedUrls(): array
    {
        return [
            'loopback v4' => ['http://127.0.0.1/'],
            'loopback v6' => ['http://[::1]/'],
            'rfc1918 10/8' => ['http://10.0.0.1/'],
            'rfc1918 192.168/16' => ['http://192.168.1.1/'],
            'rfc1918 172.16/12' => ['http://172.19.0.2/'],
            'link-local' => ['http://169.254.169.254/latest/meta-data/'],
            'cgn 100.64/10' => ['http://100.64.0.1/'],
            'unspecified 0.0.0.0' => ['http://0.0.0.0/'],
            'decimal-encoded loopback' => ['http://2130706433/'],
            'hex-encoded loopback' => ['http://0x7f000001/'],
            'ipv4-mapped ipv6 loopback' => ['http://[::ffff:127.0.0.1]/'],
            'cloud metadata hostname' => ['http://metadata.google.internal/'],
            'disallowed scheme (file)' => ['file:///etc/passwd'],
            'disallowed scheme (gopher)' => ['gopher://127.0.0.1/'],
            'userinfo present' => ['http://user:pass@example.com/'],
            'malformed url' => ['not a url'],
        ];
    }

    #[Test]
    public function it_allows_public_ipv4(): void
    {
        $guard = app(SsrfGuard::class);

        $guard->assertSafeUrl('http://8.8.8.8/');

        $this->addToAssertionCount(1);
    }

    #[Test]
    public function it_honours_the_allowed_hosts_list(): void
    {
        config()->set('core.ssrf.allowed_hosts', ['10.0.0.5']);

        $guard = app(SsrfGuard::class);

        $guard->assertSafeUrl('http://10.0.0.5/internal');

        $this->addToAssertionCount(1);
    }
}
