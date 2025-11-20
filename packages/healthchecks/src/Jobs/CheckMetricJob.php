<?php

namespace Vigilant\Healthchecks\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Vigilant\Core\Services\TeamService;
use Vigilant\Healthchecks\Actions\CheckMetric;
use Vigilant\Healthchecks\Models\Healthcheck;

class CheckMetricJob implements ShouldBeUnique, ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(public Healthcheck $healthcheck, public int $runId)
    {
        $this->onQueue(config()->string('healthchecks.queue'));
    }

    public function handle(CheckMetric $checkMetric, TeamService $teamService): void
    {
        $teamService->setTeamById($this->healthcheck->team_id);
        $checkMetric->check($this->healthcheck, $this->runId);
    }

    public function uniqueId(): string
    {
        return 'metric-'.$this->healthcheck->id.'-'.$this->runId;
    }
}
