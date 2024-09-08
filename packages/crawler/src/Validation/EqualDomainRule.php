<?php

namespace Vigilant\Crawler\Validation;

use Closure;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\InvokableRule;

class EqualDomainRule implements DataAwareRule, InvokableRule
{
    protected array $data = [];

    public function setData(array $data): static
    {
        $this->data = $data;

        return $this;
    }

    public function __invoke(string $attribute, mixed $value, Closure $fail): void
    {
        $startUrl = $this->data['start_url'] ?? null;
        $sitemaps = $this->data['sitemaps'] ?? [];

        if ($startUrl && ! empty($sitemaps)) {
            $startUrlDomain = parse_url($startUrl, PHP_URL_HOST);

            foreach ($sitemaps as $sitemap) {
                $sitemapDomain = parse_url($sitemap, PHP_URL_HOST);

                if ($sitemapDomain !== $startUrlDomain) {
                    $fail(__('The domains of the start URL and sitemaps must be the same domain'));
                    return;
                }
            }
        }
    }
}
