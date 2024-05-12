<?php

namespace Vigilant\Lighthouse\Commands;

use Cron\CronExpression;
use Illuminate\Console\Command;
use Vigilant\Lighthouse\Jobs\LighthouseJob;
use Vigilant\Lighthouse\Models\LighthouseSite;

class ScheduleLighthouseCommand extends Command
{
    protected $signature = 'lighthouse:schedule';

    protected $description = 'Schedule Lighthouse Jobs';

    public function handle(): int
    {
        LighthouseSite::query()
            ->withoutGlobalScopes()
            ->get()
            ->each(function (LighthouseSite $site) {

                if (CronExpression::isValidExpression($site->interval)) {
                    $expression = new CronExpression($site->interval);

                    if ($expression->isDue(now())) {
                        LighthouseJob::dispatch($site);
                    }
                }
            });

        return static::SUCCESS;
    }
}
