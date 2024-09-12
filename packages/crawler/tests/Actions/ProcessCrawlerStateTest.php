<?php

namespace Vigilant\Crawler\Tests\Actions;

use Illuminate\Support\Facades\Event;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\Test;
use Vigilant\Crawler\Actions\ProcessCrawlerState;
use Vigilant\Crawler\Actions\StartCrawler;
use Vigilant\Crawler\Enums\State;
use Vigilant\Crawler\Events\CrawlerFinishedEvent;
use Vigilant\Crawler\Models\Crawler;
use Vigilant\Crawler\Tests\TestCase;

class ProcessCrawlerStateTest extends TestCase
{
    #[Test]
    public function it_processes_pending_state(): void
    {
        $this->mock(StartCrawler::class, function (MockInterface $mock): void {
            $mock->shouldReceive('start')->andReturn()->once();
        });

        /** @var Crawler $crawler */
        $crawler = Crawler::query()->create([
            'start_url' => 'vigilant',
            'state' => State::Pending,
            'schedule' => '0 0 * * *',
        ]);

        /** @var ProcessCrawlerState $action */
        $action = app(ProcessCrawlerState::class);
        $action->process($crawler);
    }

    #[Test]
    public function it_processes_crawling_finished_state(): void
    {
        Event::fake([CrawlerFinishedEvent::class]);

        /** @var Crawler $crawler */
        $crawler = Crawler::query()->create([
            'start_url' => 'vigilant',
            'state' => State::Crawling,
            'schedule' => '0 0 * * *',
        ]);

        $crawler->urls()->create([
            'url' => 'vigilant/url-1',
            'crawled' => true,
        ]);

        /** @var ProcessCrawlerState $action */
        $action = app(ProcessCrawlerState::class);
        $action->process($crawler);

        $crawler->refresh();

        $this->assertEquals(State::Finished, $crawler->state);
        Event::assertDispatched(CrawlerFinishedEvent::class);
    }
}
