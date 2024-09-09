<?php

namespace Vigilant\Crawler\Tests\Actions;

use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\Test;
use Vigilant\Crawler\Actions\CrawlUrl;
use Vigilant\Crawler\Enums\State;
use Vigilant\Crawler\Models\CrawledUrl;
use Vigilant\Crawler\Models\Crawler;
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
           </html>'),
        ])->preventStrayRequests();

        /** @var Crawler $crawler */
        $crawler = Crawler::query()->create([
            'start_url' => 'vigilant',
            'state' => State::Crawling,
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

        $this->assertEquals([
            'https://govigilant.io/relative-url',
            'https://govigilant.io/trailing-url',
            'http://govigilant.io/unsecure-url',
            'https://govigilant.io',
        ], $foundUrls);
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
        $this->assertEquals(1, $crawler->urls()->count());
        $this->assertTrue($crawledUrl->crawled);
    }

    #[Test]
    public function it_handles_ratelimiting(): void
    {
        Http::fake([
            'https://govigilant.io/url-1' => Http::response('', 429),
        ])->preventStrayRequests();

        /** @var Crawler $crawler */
        $crawler = Crawler::query()->create([
            'start_url' => 'vigilant',
            'state' => State::Crawling,
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
}
