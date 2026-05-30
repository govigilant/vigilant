<?php

namespace Vigilant\Crawler\Tests\Actions;

use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\Test;
use Vigilant\Crawler\Actions\CrawlUrl;
use Vigilant\Crawler\Enums\State;
use Vigilant\Crawler\Models\CrawledUrl;
use Vigilant\Crawler\Models\Crawler;
use Vigilant\Crawler\Tests\TestCase;

class CrawlUrlSsrfTest extends TestCase
{
    #[Test]
    public function it_blocks_internal_start_urls(): void
    {
        Http::preventStrayRequests();

        /** @var Crawler $crawler */
        $crawler = Crawler::query()->create([
            'start_url' => 'http://127.0.0.1',
            'state' => State::Crawling,
            'schedule' => '0 0 * * *',
        ]);

        /** @var CrawledUrl $crawledUrl */
        $crawledUrl = $crawler->urls()->create([
            'url' => 'http://127.0.0.1/admin',
            'crawled' => false,
        ]);

        /** @var CrawlUrl $action */
        $action = app(CrawlUrl::class);
        $action->crawl($crawledUrl);

        $crawledUrl->refresh();

        $this->assertSame(0, $crawledUrl->status);
        $this->assertTrue($crawledUrl->crawled);
        Http::assertNothingSent();
    }

    #[Test]
    public function it_blocks_cloud_metadata_addresses(): void
    {
        Http::preventStrayRequests();

        /** @var Crawler $crawler */
        $crawler = Crawler::query()->create([
            'start_url' => 'http://169.254.169.254',
            'state' => State::Crawling,
            'schedule' => '0 0 * * *',
        ]);

        /** @var CrawledUrl $crawledUrl */
        $crawledUrl = $crawler->urls()->create([
            'url' => 'http://169.254.169.254/latest/meta-data/',
            'crawled' => false,
        ]);

        /** @var CrawlUrl $action */
        $action = app(CrawlUrl::class);
        $action->crawl($crawledUrl);

        $crawledUrl->refresh();

        $this->assertSame(0, $crawledUrl->status);
        $this->assertTrue($crawledUrl->crawled);
        Http::assertNothingSent();
    }
}
