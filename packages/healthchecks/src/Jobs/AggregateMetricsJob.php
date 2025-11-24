<?php

namespace Vigilant\Healthchecks\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Vigilant\Healthchecks\Actions\AggregateMetrics;

class AggregateMetricsJob implements ShouldBeUnique, ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct()
    {
        $this->onQueue(config()->string('healthchecks.queue'));
    }

    public function handle(AggregateMetrics $aggregateMetrics): void
    {
        $aggregateMetrics->handle();
    }

    public function uniqueId(): string
    {
        return 'aggregate-metrics';
    }
}
