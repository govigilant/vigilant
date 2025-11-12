<?php

namespace Vigilant\Healthchecks\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Vigilant\Core\Services\TeamService;
use Vigilant\Healthchecks\Actions\CheckResult;
use Vigilant\Healthchecks\Models\Healthcheck;

class CheckResultJob implements ShouldBeUnique, ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(public Healthcheck $healthcheck, public int $runId)
    {
        $this->onQueue(config()->string('healthchecks.queue'));
    }

    public function handle(CheckResult $result, TeamService $teamService): void
    {
        $teamService->setTeamById($this->healthcheck->team_id);
        $result->check($this->healthcheck, $this->runId);
    }

    public function uniqueId(): string
    {
        return $this->healthcheck->id.'-'.$this->runId;
    }
}
