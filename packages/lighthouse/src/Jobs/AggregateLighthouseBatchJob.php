<?php

namespace Vigilant\Lighthouse\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Vigilant\Core\Services\TeamService;
use Vigilant\Lighthouse\Actions\AggregateLighthouseBatch;
use Vigilant\Lighthouse\Models\LighthouseMonitor;

class AggregateLighthouseBatchJob implements ShouldBeUnique, ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(public LighthouseMonitor $site, public string $batchId)
    {
        $this->onQueue(config('lighthouse.queue'));
    }

    public function handle(TeamService $teamService, AggregateLighthouseBatch $aggregator): void
    {
        $teamService->setTeamById($this->site->team_id);

        $aggregator->aggregateBatch($this->site, $this->batchId);

    }

    public function uniqueId(): int
    {
        return $this->site->id;
    }
}
