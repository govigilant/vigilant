<?php

namespace Vigilant\Crawler\Tests\Actions;

use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\Test;
use Vigilant\Crawler\Actions\CrawlUrl;
use Vigilant\Crawler\Enums\State;
use Vigilant\Crawler\Models\CrawledUrl;
use Vigilant\Crawler\Models\Crawler;
use Vigilant\Crawler\Notifications\RatelimitedNotification;
use Vigilant\Crawler\Tests\TestCase;

class CrawUrlTest extends TestCase
{
    #[Test]
    public function it_crawls_url(): void
    {
        Http::fake([
            'https://govigilant.io/url-1' => Http::response('<html>
            <a href="/relative-url"></a>
            <a href="/trailing-url/#"></a>
            <a href="http://govigilant.io/unsecure-url"></a>
            <a href="#"></a>
            <a href="/"></a>
            <a href="tel:+123"></a>
            <a href="mailto:vincent@govigilant.io"></a>
           </html>'),
        ])->preventStrayRequests();

        /** @var Crawler $crawler */
        $crawler = Crawler::query()->create([
            'start_url' => 'vigilant',
            'state' => State::Crawling,
            'schedule' => '0 0 * * *',
        ]);

        /** @var CrawledUrl $crawledUrl */
        $crawledUrl = $crawler->urls()->create([
            'url' => 'https://govigilant.io/url-1',
            'crawled' => false,
        ]);

        /** @var CrawlUrl $action */
        $action = app(CrawlUrl::class);
        $action->crawl($crawledUrl);

        $crawledUrl->refresh();
        $this->assertEquals(200, $crawledUrl->status);
        $this->assertTrue($crawledUrl->crawled);

        $foundUrls = $crawler->urls()
            ->where('crawled', '=', false)
            ->pluck('url')
            ->toArray();

        $discoveredUrls = array_values(array_filter(
            $foundUrls,
            fn (string $url): bool => $url !== $crawler->start_url,
        ));

        $this->assertEquals([
            'https://govigilant.io/relative-url',
            'https://govigilant.io/trailing-url',
            'http://govigilant.io/unsecure-url',
            'https://govigilant.io',
        ], $discoveredUrls);
    }

    #[Test]
    public function it_handles_malformed_html(): void
    {
        Http::fake([
            'https://govigilant.io/url-1' => Http::response('<html>
         <div><a hrefno123!#@!><<<><>
           </htl'),
        ])->preventStrayRequests();

        /** @var Crawler $crawler */
        $crawler = Crawler::query()->create([
            'start_url' => 'vigilant',
            'state' => State::Crawling,
            'schedule' => '0 0 * * *',
        ]);

        /** @var CrawledUrl $crawledUrl */
        $crawledUrl = $crawler->urls()->create([
            'url' => 'https://govigilant.io/url-1',
            'crawled' => false,
        ]);

        /** @var CrawlUrl $action */
        $action = app(CrawlUrl::class);
        $action->crawl($crawledUrl);

        $crawledUrl->refresh();
        $this->assertEquals(2, $crawler->urls()->count());
        $this->assertTrue($crawledUrl->crawled);
    }

    #[Test]
    public function it_handles_ratelimiting(): void
    {
        RatelimitedNotification::fake();
        Http::fake([
            'https://govigilant.io/url-1' => Http::response('', 429),
        ])->preventStrayRequests();

        /** @var Crawler $crawler */
        $crawler = Crawler::query()->create([
            'start_url' => 'vigilant',
            'state' => State::Crawling,
            'schedule' => '0 0 * * *',
        ]);

        /** @var CrawledUrl $crawledUrl */
        $crawledUrl = $crawler->urls()->create([
            'url' => 'https://govigilant.io/url-1',
            'crawled' => false,
        ]);

        /** @var CrawlUrl $action */
        $action = app(CrawlUrl::class);
        $action->crawl($crawledUrl);

        $crawler->refresh();
        $this->assertEquals(State::Ratelimited, $crawler->state);
        $this->assertTrue($crawledUrl->crawled);
    }

    #[Test]
    public function it_does_not_insert_blacklisted_urls(): void
    {
        Http::fake([
            'https://govigilant.io/url-1' => Http::response('<html>
            <a href="/products/shoes"></a>
            <a href="/checkout/cart"></a>
            <a href="/customer/account/login"></a>
            <a href="/about-us"></a>
           </html>'),
        ])->preventStrayRequests();

        /** @var Crawler $crawler */
        $crawler = Crawler::query()->create([
            'start_url' => 'https://govigilant.io',
            'state' => State::Crawling,
            'schedule' => '0 0 * * *',
            'settings' => [
                'url_blacklist' => implode("\n", [
                    '~^https?://[^/]+/checkout/~i',
                    '~^https?://[^/]+/customer/account/login~i',
                ]),
            ],
        ]);

        /** @var CrawledUrl $crawledUrl */
        $crawledUrl = $crawler->urls()->create([
            'url' => 'https://govigilant.io/url-1',
            'crawled' => false,
        ]);

        /** @var CrawlUrl $action */
        $action = app(CrawlUrl::class);
        $action->crawl($crawledUrl);

        $discoveredUrls = $crawler->urls()
            ->where('crawled', '=', false)
            ->pluck('url')
            ->toArray();

        $this->assertContains('https://govigilant.io/products/shoes', $discoveredUrls);
        $this->assertContains('https://govigilant.io/about-us', $discoveredUrls);
        $this->assertNotContains('https://govigilant.io/checkout/cart', $discoveredUrls);
        $this->assertNotContains('https://govigilant.io/customer/account/login', $discoveredUrls);
    }

    #[Test]
    public function it_inserts_all_urls_when_blacklist_is_empty(): void
    {
        Http::fake([
            'https://govigilant.io/url-1' => Http::response('<html>
            <a href="/checkout/cart"></a>
            <a href="/about-us"></a>
           </html>'),
        ])->preventStrayRequests();

        /** @var Crawler $crawler */
        $crawler = Crawler::query()->create([
            'start_url' => 'https://govigilant.io',
            'state' => State::Crawling,
            'schedule' => '0 0 * * *',
        ]);

        /** @var CrawledUrl $crawledUrl */
        $crawledUrl = $crawler->urls()->create([
            'url' => 'https://govigilant.io/url-1',
            'crawled' => false,
        ]);

        /** @var CrawlUrl $action */
        $action = app(CrawlUrl::class);
        $action->crawl($crawledUrl);

        $discoveredUrls = $crawler->urls()
            ->where('crawled', '=', false)
            ->pluck('url')
            ->toArray();

        $this->assertContains('https://govigilant.io/checkout/cart', $discoveredUrls);
        $this->assertContains('https://govigilant.io/about-us', $discoveredUrls);
    }
}
