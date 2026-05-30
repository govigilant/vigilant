<?php

namespace Vigilant\Core\Http\Exceptions;

use RuntimeException;

class SsrfException extends RuntimeException
{
    public static function invalidUrl(string $url): self
    {
        return new self(sprintf('The URL "%s" is not a valid absolute URL.', $url));
    }

    public static function disallowedScheme(string $scheme): self
    {
        return new self(sprintf('The URL scheme "%s" is not allowed.', $scheme));
    }

    public static function userInfoForbidden(): self
    {
        return new self('URLs containing userinfo are not allowed.');
    }

    public static function blockedHost(string $host): self
    {
        return new self(sprintf('The host "%s" is not allowed.', $host));
    }

    public static function blockedIp(string $ip): self
    {
        return new self(sprintf('The host resolves to a non-routable or reserved IP address "%s".', $ip));
    }

    public static function unresolvable(string $host): self
    {
        return new self(sprintf('The host "%s" could not be resolved.', $host));
    }
}
