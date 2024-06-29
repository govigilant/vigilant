<?php

namespace Vigilant\Lighthouse\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Vigilant\Core\Services\TeamService;
use Vigilant\Lighthouse\Actions\AggregateResults;
use Vigilant\Lighthouse\Models\LighthouseSite;

class AggregateLighthouseResultsJob implements ShouldBeUnique, ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        public LighthouseSite $site,
        public Carbon $from,
        public Carbon $till,
    ) {}

    public function handle(AggregateResults $aggregateResults, TeamService $teamService): void
    {
        $teamService->setTeamById($this->site->team_id);
        $aggregateResults->aggregate(
            $this->site,
            $this->from,
            $this->till
        );
    }

    public function uniqueId(): string
    {
        return $this->site->id.$this->from->getTimestamp();
    }
}
