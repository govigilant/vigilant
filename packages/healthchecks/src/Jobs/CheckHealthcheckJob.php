<?php

namespace Vigilant\Healthchecks\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Vigilant\Core\Services\TeamService;
use Vigilant\Healthchecks\Actions\CheckHealth;
use Vigilant\Healthchecks\Models\Healthcheck;

class CheckHealthcheckJob implements ShouldBeUnique, ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(public Healthcheck $healthcheck)
    {
        $this->onQueue(config()->string('healthchecks.queue'));
    }

    public function handle(CheckHealth $checkHealth, TeamService $teamService): void
    {
        $teamService->setTeamById($this->healthcheck->team_id);
        $checkHealth->check($this->healthcheck);
    }

    public function uniqueId(): int
    {
        return $this->healthcheck->id;
    }
}
