<?php

namespace Vigilant\Crawler\Tests\Actions;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use PHPUnit\Framework\Attributes\Test;
use Vigilant\Crawler\Actions\ImportSitemaps;
use Vigilant\Crawler\Enums\State;
use Vigilant\Crawler\Models\Crawler;
use Vigilant\Crawler\Tests\TestCase;

class ImportSitemapsSsrfTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Avoid triggering the StartCrawler/ImportSitemaps jobs that the
        // Crawler observer dispatches on create.
        Queue::fake();
    }

    #[Test]
    public function it_blocks_internal_sitemap_urls(): void
    {
        Http::preventStrayRequests();

        /** @var Crawler $crawler */
        $crawler = Crawler::query()->create([
            'start_url' => 'http://127.0.0.1',
            'sitemaps' => ['http://127.0.0.1/sitemap.xml'],
            'state' => State::Crawling,
            'schedule' => '0 0 * * *',
        ]);

        /** @var ImportSitemaps $action */
        $action = app(ImportSitemaps::class);
        $action->import($crawler);

        $this->assertSame(0, $crawler->urls()->count());
        Http::assertNothingSent();
    }

    #[Test]
    public function it_does_not_follow_nested_sitemaps_pointing_at_internal_hosts(): void
    {
        Http::fake([
            'https://govigilant.io/sitemap.xml' => Http::response(<<<'XML'
                <?xml version="1.0" encoding="UTF-8"?>
                <sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
                    <sitemap><loc>http://169.254.169.254/latest/meta-data/</loc></sitemap>
                </sitemapindex>
                XML, 200, ['Content-Type' => 'application/xml']),
        ])->preventStrayRequests();

        /** @var Crawler $crawler */
        $crawler = Crawler::query()->create([
            'start_url' => 'https://govigilant.io',
            'sitemaps' => ['https://govigilant.io/sitemap.xml'],
            'state' => State::Crawling,
            'schedule' => '0 0 * * *',
        ]);

        config()->set('core.ssrf.allowed_hosts', ['govigilant.io']);

        /** @var ImportSitemaps $action */
        $action = app(ImportSitemaps::class);
        $action->import($crawler);

        Http::assertSentCount(1);
        Http::assertSent(fn ($request): bool => $request->url() === 'https://govigilant.io/sitemap.xml');
        $this->assertSame(0, $crawler->urls()->count());
    }
}
