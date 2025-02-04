<?php

namespace Vigilant\Crawler\Commands;

use Cron\CronExpression;
use Illuminate\Console\Command;
use Vigilant\Crawler\Actions\StartCrawler;
use Vigilant\Crawler\Enums\State;
use Vigilant\Crawler\Models\Crawler;

class ScheduleCrawlersCommand extends Command
{
    protected $signature = 'crawler:schedule';

    protected $description = 'Schedule Crawling Jobs';

    public function handle(StartCrawler $starter): int
    {
        Crawler::query()
            ->withoutGlobalScopes()
            ->where('enabled', '=', true)
            ->where('state', '!=', State::Crawling)
            ->get()
            ->each(function (Crawler $crawler) use ($starter) {
                if (CronExpression::isValidExpression($crawler->schedule)) {

                    $expression = new CronExpression($crawler->schedule);

                    if ($expression->isDue(now())) {
                        $starter->start($crawler);
                    }

                }
            });

        return static::SUCCESS;
    }
}
