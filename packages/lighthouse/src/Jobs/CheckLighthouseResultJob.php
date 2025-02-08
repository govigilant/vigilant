<?php

namespace Vigilant\Lighthouse\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Vigilant\Core\Services\TeamService;
use Vigilant\Lighthouse\Actions\CheckLighthouseResult;
use Vigilant\Lighthouse\Models\LighthouseResult;

class CheckLighthouseResultJob implements ShouldBeUnique, ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(public LighthouseResult $result)
    {
        $this->onQueue(config('lighthouse.queue'));
    }

    public function handle(CheckLighthouseResult $checker, TeamService $teamService): void
    {
        $teamService->setTeamById($this->result->lighthouseSite->team_id);
        $checker->check($this->result);
    }

    public function uniqueId(): int
    {
        return $this->result->id;
    }
}
