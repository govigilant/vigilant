<?php

namespace Vigilant\Uptime\Commands;

use Cron\CronExpression;
use Illuminate\Console\Command;
use Vigilant\Crawler\Actions\StartCrawler;
use Vigilant\Crawler\Models\Crawler;

class ScheduleCrawlersCommand extends Command
{
    protected $signature = 'crawler:schedule';

    protected $description = 'Schedule Crawling Jobs';

    public function handle(StartCrawler $starter): int
    {
        Crawler::query()
            ->withoutGlobalScopes()
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
